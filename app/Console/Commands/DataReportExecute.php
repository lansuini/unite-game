<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TransferInOut;
use App\Http\Library\MerchantCF;
use App\Models\Customer;
use Artisan;

class DataReportExecute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DataReportExecute {day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DataReport Creator';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $day = $this->argument('day');
        $mcb = new MerchantCF();
        $message = '';
        if ($day == 'cron') {
            $start = microtime(true);
            $nday = date('Y-m-d', strtotime('-1 hours'));
            $message .= 'run:' . $nday . PHP_EOL;
            $mcb->calDataReport($nday);

            $today = date('Y-m-d');
            if ($today != $nday) {
                $message .= 'run:' . $today . PHP_EOL;
                $mcb->calDataReport($today);
            }
            $cost = intval((microtime(true) - $start) * 1000);


            $hostname = gethostname();
            $message .= 'DataReport cost_time(ms): ' . $cost . PHP_EOL;
            if (intval(date('H')) > 22) {
                Artisan::queue('SendMessage', [
                    'text' => "[" . date('Y-m-d H:i:s') . "]" . '[' . $hostname . ']' . $message
                ])->onConnection('redis')->onQueue('default');
                echo $message;
            }


        } else if ($day == 'all') {
            $customer = new Customer();
            $c = $customer->get();
            foreach ($c as $v) {
                $transferInOut = new TransferInOut;
                $transferInOut->setTable('transfer_inout_' . $v->id);
                $s = $transferInOut->orderBy('id', 'asc')->first();
                $e = $transferInOut->orderBy('id', 'desc')->first();

                if (!empty($s)) {
                    $start = strtotime($s->create_time);
                    $end = strtotime($e->create_time);
                    $n = ceil(($end - $start) / 86400);

                    for ($i = 0; $i < $n; $i++) {
                        echo 'run:' . date('Y-m-d', $start + $i * 86400) . PHP_EOL;
                        $mcb->calDataReport(date('Y-m-d', $start + $i * 86400));
                    }
                }
            }
        } else {
            echo 'run:' . $day . PHP_EOL;
            $mcb->calDataReport($day);
        }
        echo 'Finish' . PHP_EOL;
        return 0;
    }
}
