<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Library\MerchantCB;
use App\Models\ServerRequestLog;
use Illuminate\Support\Facades\Log;
use Artisan;

class CashTransferInOutNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CashTransferInOutNotify {clientId} {pid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CashTransferInOutNotify task';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('clientId');
        $pid = $this->argument('pid');

        if ($clientId == 0 || $pid == 0) {
            Log::error("CashTransferInOutNotifyCommand", ['client_id' => $clientId, 'pid' => $pid]);
            return 0;
        }

        $serverRequestLog = new ServerRequestLog;
        $data = $serverRequestLog->setTable('server_request_log_' . $clientId)->where('id', $pid)->first();

        $mcb = new MerchantCB();
        $args = json_decode($data->args, true);
        if (empty($args)) {
            Log::error("CashTransferInOutNotifyCommand", ['client_id' => $clientId, 'pid' => $pid]);
            return 0;
        }

        $res = $mcb->getCashTransferInOut($args[0], $pid);

        $error = $res['error'] ?? null;
        // Log::info('CashTransferInOutNotify ' . $pid, $res);

        // $clientId = $args['client_id'] ?? 0;
        // $uid = $args['player_uid'] ?? 0;
        $queue = $mcb->getQueueNameByFailTask($clientId, $data->uid);

        if ($error != null) {
            $serverRequestLog = new ServerRequestLog;
            $count = $serverRequestLog->setTable('server_request_log_' . $clientId)->where('pid', $pid)->count();
            if ($count <= 5) {
                // return;
                Artisan::queue('CashTransferInOutNotify', [
                    'clientId' => $clientId,
                    'pid' => $pid,
                ])->onConnection('redis')->onQueue($queue)->delay(pow(5, $count + 1));
            }
        }
        return 0;
    }
}
