<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Library\MerchantCB;
use App\Models\Customer;
use App\Models\TransferInOut;
use App\Models\TransferInOutServerRequestLog;
use Illuminate\Support\Facades\Log;
use Artisan;

/**
 * php artisan RetryNotify --start='2023-04-22 00:00:00' --end='2023-04-22 23:59:59'
 * php artisan RetryNotify
 */
class RetryNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RetryNotify {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RetryNotify';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $size = 100;
        $customers = Customer::where('api_mode', 0)->get();
        $mcb = new MerchantCB();
        $startTime = microtime(true);

        $start = $this->option('start');
        $end = $this->option('end');
        // dd([$start, $end]);
        $start = $start ?? date('Y-m-d 00:00:00', time() - 90 * 60);
        $end =  $end ?? date('Y-m-d H:i:s', time() - 30 * 60);

        $num = 0;
        foreach ($customers as $customer) {
            $clientId = $customer->id;
            $page = 1;
            $transferInOut = new TransferInOut;
            $transferInOut->setTable('transfer_inout_' . $clientId);

            $transferInOutServerRequestLog = new TransferInOutServerRequestLog;
            $transferInOutServerRequestLog->setTable('transfer_inout_server_request_log_' . $clientId);

            while (true) {
                $res = $transferInOut->where('create_time', '>=', $start)->where('create_time', '<=', $end)->where('status', 0)->forPage($page, $size)->get()->toArray();
                if (empty($res)) {
                    break;
                }
                // dd($res);
                foreach ($res as $v) {
                    // $v = $v->toArray();
                    $queue = $mcb->getQueueName($clientId, $v['player_uid']);
                    $r = $transferInOutServerRequestLog->where('transaction_id', $v['transaction_id'])->first();
                    // echo $v['transaction_id'].'|'.$queue . PHP_EOL;
                    if (empty($r)) {
                        \App\Jobs\CashTransferInOutNotifyBatch::dispatch($v)->onQueue($queue);
                        $num++;
                    }
                    // break;

                }

                // dd($res);
                // break;
                // echo 'Client:' . $clientId . ' Page:' . $page . PHP_EOL;
                $page++;
            }
        }

        $costTime = intval((microtime(true) - $startTime) * 1000);
        echo "Finish($num) CostTime(ms):" . $costTime . PHP_EOL;

        if ($num > 0) {
            $hostname = gethostname();
            Artisan::queue('SendMessage', [
                'text' => "[" . date('Y-m-d H:i:s') . "]" . '[' . $hostname . '] RetryNotify Num: ' . $num . ' CostTime(ms):' . $costTime
            ])->onConnection('redis')->onQueue('default');
        }
    }
}
