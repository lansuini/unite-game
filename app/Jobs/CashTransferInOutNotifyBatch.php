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

class CashTransferInOutNotifyBatch implements ShouldQueue
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
        if (isset($this->payload['getResult'])) {
            unset($this->payload['getResult']);
            $this->getResult($this->payload);
        } else {
            $this->noResult($this->payload);
        }
        // Log::info("CashTransferInOutNotifyBatch", [$this->payload["transaction_id"] ?? 'error']);
    }

    protected function noResult($data)
    {
        $mcb = new MerchantCB();
        $redis = Redis::connection();
        $mcb->setType('server');
        $mcb->setQueueName($this->queue);
        $return = $mcb->getCashTransferInOut($data);
        $uid = $data['player_uid'] ?? 0;
        $clientId = (int) $redis->hget($uid, 'play_type_id');
        $queue = $mcb->getQueueNameByFailTask($clientId, $uid);

        if ($mcb->isNeedToRetry($return)) {
            $serverRequestLog = $mcb->getCashTransferInOutServerRequestLog();
            // Artisan::queue('CashTransferInOutNotify', [
            //     'clientId' => $clientId,
            //     'pid' => $serverRequestLog->getId(),
            // ])->onConnection('redis')->onQueue($queue)->delay(5);
            
            CashTransferInOutNotify::dispatch([
                'clientId' => $clientId,
                'pid' => $serverRequestLog->getId(),
            ])->onQueue($queue)->delay(5);
        }
    }

    public function getResult($data)
    {
        $mcb = new MerchantCB();
        $redis = Redis::connection();
        $mcb->setType('server');
        $redis = Redis::connection('cache');
        $mcb->setQueueName($this->queue);
        $return = $mcb->getCashTransferInOut($data);  
        $uid = $data['player_uid'] ?? 0;
        $clientId = (int) $redis->hget($uid, 'play_type_id');
        $queue = $mcb->getQueueNameByFailTask($clientId, $uid);

        if ($mcb->isNeedToRetry($return)) {
            $serverRequestLog = $mcb->getCashTransferInOutServerRequestLog();
            // Artisan::queue('CashTransferInOutNotify', [
            //     'clientId' => $clientId,
            //     'pid' => $serverRequestLog->getId(),
            // ])->onConnection('redis')->onQueue($queue)->delay(5);
            CashTransferInOutNotify::dispatch([
                'clientId' => $clientId,
                'pid' => $serverRequestLog->getId(),
            ])->onQueue($queue)->delay(5);

        }
        $redis->setex(md5($data['transaction_id']), intval(env('API_REQUEST_TIME_OUT', 8.0) + 5), json_encode($return));
    }
}
