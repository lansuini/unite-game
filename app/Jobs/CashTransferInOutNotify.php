<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Http\Library\MerchantCB;
use App\Models\ServerRequestLog;
// use Illuminate\Support\Facades\Log;
use Artisan;

class CashTransferInOutNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

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
        $clientId = $this->payload['clientId'] ?? 0;
        $pid = $this->payload['pid'] ?? 0;

        if ($clientId == 0 || $pid == 0) {
            Log::error("CashTransferInOutNotifyJobs", ['client_id' => $clientId, 'pid' => $pid]);
            return 0;
        }

        $serverRequestLog = new ServerRequestLog;
        $data = $serverRequestLog->setTable('server_request_log_' . $clientId)->where('id', $pid)->first();

        $mcb = new MerchantCB();
        $args = json_decode($data->args, true);
        if (empty($args)) {
            Log::error("CashTransferInOutNotifyJobs", ['client_id' => $clientId, 'pid' => $pid]);
            return 0;
        }

        $mcb->setQueueName($this->queue);
        $res = $mcb->getCashTransferInOut($args[0], $pid);

        $error = $res['error'] ?? null;
        // Log::info('CashTransferInOutNotify ' . $pid, $res);

        $queue = $mcb->getQueueNameByFailTask($clientId, $data->uid);

        if ($error != null) {
            $serverRequestLog = new ServerRequestLog;
            $count = $serverRequestLog->setTable('server_request_log_' . $clientId)->where('pid', $pid)->count();
            if ($count <= 5) {
                // return;
                // Artisan::queue('CashTransferInOutNotify', [
                //     'clientId' => $clientId,
                //     'pid' => $pid,
                // ])->onConnection('redis')->onQueue($queue)->delay(pow(5, $count + 1));

                
                CashTransferInOutNotify::dispatch([
                    'clientId' => $clientId,
                    'pid' => $pid,
                ])->onQueue($queue)->delay(pow(5, $count + 1));
            }
        }
        return 0;
    }

}
