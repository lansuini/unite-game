<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use App\Http\Library\MerchantCB;
use App\Models\Manager\Analysis\ExportFileLog;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use Artisan;

class ExportFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    protected $error;

    protected $typeData = [];

    protected function getColumnsCMD()
    {
        return [
            'C00000' => function ($row, $value) {
                return $value;
            },
            'C00001' => function ($row, $value) {
                return sprintf('%.2f', (intval($value) / 100));
            },
            'C00002' => function ($row, $value) {
                return sprintf('%.2f', (intval($value) / 100)) . '%';
            },
            'C00003' => function ($row, $value) {
                foreach ($this->typeData['gameAliasType'] ?? [] as $v) {
                    if ($v['key'] == $value) {
                        return $v['value'];
                    }
                }
                return $value;
            },
            'C00004' => function ($row, $value) {
                foreach ($this->typeData['customerType'] ?? [] as $k => $v) {
                    if ($k == $value) {
                        return $v;
                    }
                }
                return $value;
            },
            'C00005' => function ($row, $value) {
                foreach ($this->typeData['customerSubType2'] ?? [] as $k => $v) {
                    if ($k == $value) {
                        return $v;
                    }
                }
                return $value;
            }
        ];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->error = '';
        $start = microtime(true);
        // Log::info('ExportFile payload', ['payload' => $this->payload]);
        $model = $this->payload['model'];
        $limit = 1000;
        $page = 0;
        $maxPage = 100;
        $this->getTypeData();
        // Log::info('ExportFile typeData', ['typeData' => $this->typeData]);
        $columnCMD = $this->getColumnsCMD();
        $storagePath = storage_path('export_file' . '/' . $this->payload['proj'] . '/' . date('Ymd'));
        if (!File::isDirectory($storagePath)) {
            File::makeDirectory($storagePath, 0777, true, true);
        }
        $filePath = $storagePath . '/' . $this->payload['model']->key . '_' . $this->payload['model']->id . '.csv';
        $fp = fopen($filePath, 'w');
        while (true) {
            if ($page == 0) {
                fputcsv($fp, $this->getHeaderColumns());
            }

            $data = $this->request(
                $this->payload['baseuri'],
                $this->payload['uri'],
                (array) $this->payload['query'],
                $page * $limit,
                $limit
            );

            // Log::debug('ExportFileJobs@data', $data);

            if (!empty($this->error)) {
                // Log::debug('ExportFileJobs@1');
                break;
            }

            if (!isset($data['rows']) || empty($data['total'])) {
                // Log::debug('ExportFileJobs@11');
                break;
            }
            
            foreach ($data['rows'] ?? [] as $row) {
                $temp = [];
                foreach ($this->payload['columns'] as $column) {
                    $isContinue = $column['isContinue'] ?? 0;
                    if ($isContinue) {
                        continue;
                    }
                    $value = $this->getValue($columnCMD, $column, $row, $row[$column['field']] ?? '');
                    $temp[] = $value;
                }
                // break;
                // Log::debug('ExportFileJobs@write', $temp);
                fputcsv($fp, $temp);
            }

            if (isset($data['total']) && ceil($data['total'] / $limit) - 1 == $page) {
                // Log::debug('ExportFileJobs@2');
                break;
            }

            if (isset($data['total']) && $data['total'] == 0) {
                // Log::debug('ExportFileJobs@3');
                break;
            }

            if ($page > $maxPage) {
                // Log::debug('ExportFileJobs@4');
                break;
            }

            if (!empty($data['rows']) && count($data['rows']) < $limit) {
                // Log::debug('ExportFileJobs@5');
                break;
            }

            if (empty($data['rows'])) {
                // Log::debug('ExportFileJobs@6');
                break;
            }
            $page++;
            usleep(1000);
        }
        fclose($fp);
        $cost = intval((microtime(true) - $start) * 1000);
        $model->ext = 'csv';
        $model->cost_time = $cost;
        $model->is_success = empty($this->error) ? 1 : 2;
        $model->errors = $this->error;
        $model->size = filesize($filePath);
        $model->update();
        // Log::info('ExportFile finsh');
    }

    protected function getTypeData()
    {
        $requireItems = !empty($this->payload['requireItems']) ? explode(',', $this->payload['requireItems']) : [];
        $selectItems = config($this->payload['baseDataPath']);
        foreach ($requireItems as $item) {
            $this->typeData[$item] = isset($selectItems[$item]) ? $selectItems[$item]() : [];
        }
    }

    protected function getValue($columnCMD, $column, $row, $value)
    {
        $typeValue = $column['typeValue'] ?? '';
        if (empty($typeValue)) {
            return $value;
        }

        if (isset($columnCMD[$typeValue])) {
            return $columnCMD[$typeValue]($row, $value);
        } else {
            return $value;
        }
    }
    protected function getHeaderColumns()
    {
        $headers = [];
        foreach ($this->payload['columns'] as $column) {
            $isContinue = $column['isContinue'] ?? 0;
            if ($isContinue) {
                continue;
            }
            $headers[] = $column['title'];
        }
        return $headers;
    }

    protected function getError()
    {
        return $this->error;
    }

    protected function request($baseuri, $uri, $query, $offset, $limit)
    {
        $redis = Redis::connection('cache');
        $query['_random'] = rand(0, 99999999);
        $redis->setex('export_random:' . $query['_random'], 30, 1);
        $query['offset'] = $offset;
        $query['limit'] = $limit;
        $query['_export'] = 1;
        $client = new Client([
            'timeout'  => 30,
            'headers' => [
                'User-Agent' => env('API_REQUEST_NAME', 'IG GAME'),
            ]
        ]);
        $a = [];
        try {
            $url = $baseuri . $uri . '?' . http_build_query($query);
            Log::info('ExportFileJobs', ['url' => $url]);
            $a = $client->request('GET', $url)->getBody()->getContents();
            $a = json_decode($a, true);
        } catch (RequestException $e) {
            $m1 = Psr7\Message::toString($e->getRequest());
            $m2 = Psr7\Message::toString($e->getResponse());
            // Log::error('ExportFile@1', [$m1 . '|' . $m2]);
            $this->error = 'RequestException:' . $m1 . '|' . $m2;
        } catch (GuzzleException $e) {
            $m1 = Psr7\Message::toString($e->getRequest());
            $m2 = $e->getMessage();
            // Log::error('ExportFile@2', [$m1 . '|' . $m2]);
            $this->error = 'GuzzleException:' . $m1 . '|' . $m2;
        }
        return $a;
    }
}
