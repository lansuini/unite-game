<?php
// $start = 0;
// $end = 11;

// $dir = '/data/hofa/pd';
// $pre = 'pd-';
// $pre2 = 'pd_';
$dir = '/data/www/tongits-php';
$pre = '';
$pre2 = '';
$tmpl1 = "
[program:{$pre}notify-{num}]
process_name=%(program_name)s_%(process_num)02d
command=php {$dir}/artisan queue:work redis --sleep=1 --tries=1 --queue={$pre2}notify_{num}
autostart=true
autorestart=true
user=www
numprocs=1
redirect_stderr=true
stdout_logfile={$dir}/storage/supervisord-notify-{num}.log
stopwaitsecs=3600";


$tmpl2 = "
[program:{$pre}fail-notify-{num}]
process_name=%(program_name)s_%(process_num)02d
command=php {$dir}/artisan queue:work redis --sleep=1 --tries=1 --queue={$pre2}fail_notify_{num}
autostart=true
autorestart=true
user=www
numprocs=1
redirect_stderr=true
stdout_logfile={$dir}/storage/supervisord-fail-notify-{num}.log
stopwaitsecs=3600";

for ($i = 0; $i < 20; $i++) {
    $tmp = $tmpl1;
    // echo str_replace('{num}', $i, $tmp) . PHP_EOL;
    echo 'php artisan queue:clear redis --queue=notify_'.$i. PHP_EOL;;
}

for ($i = 0; $i < 2; $i++) {
    $tmp = $tmpl2;
    // echo str_replace('{num}', $i, $tmp) . PHP_EOL;
    echo 'php artisan queue:clear redis --queue=fail_notify_'.$i. PHP_EOL;;
}


