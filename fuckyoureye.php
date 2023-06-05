<?php
/**
 * encrypt
 * php fuckyoureye.php -p=a12345678 -m=encrypt -t=appdev.des3
 * php fuckyoureye.php -p=a12345678 -m=encrypt
 * 
 * decrypt
 * php fuckyoureye.php -p=a12345678 -m=decrypt -t=appdev.des3
 */
$encryptDir = 'app';
$encryptDevDir = 'appdev';
$target = 'obfuscate_my_app';
runScript();

function runScript()
{
    global $encryptDir, $encryptDevDir, $target;
    $paramArrs = getopt('p:m:t:');

    $password = $paramArrs['p'] ?? '';
    $model = $paramArrs['m'] ?? '';
    $tarname = $paramArrs['t'] ?? '';
    if (empty($password) || empty($model)) {
        echo 'args is error' . PHP_EOL;
        exit;
    }

    if ($model == 'decrypt' && !is_file('appcodes/' . $tarname)) {
        echo 'args is error #2' . PHP_EOL;
        exit;
    }

    if ($model == 'encrypt' && is_dir($encryptDevDir)) {
        echo 'runtime error' . PHP_EOL;
        exit;
    }

    // if ($model == 'decrypt' && !is_dir($encryptDevDir)) {
    //     echo 'runtime error' . PHP_EOL;
    //     exit;
    // }

    if ($model == 'encrypt') {
        encrypt($password, $tarname);
    } else if ($model == 'decrypt') {
        decrypt($password, $tarname);
    }
}

function encrypt($password, $tarname)
{
    global $encryptDir, $encryptDevDir, $target;
    $affix = date('Ymd') . '.' . rand(10000, 99999);
    $command = "yakpro-po {$encryptDir} -o {$target} --no-obfuscate-function-name --no-obfuscate-class_constant-name --no-obfuscate-class-name --no-obfuscate-interface-name --no-obfuscate-trait-name --no-obfuscate-property-name --no-obfuscate-method-name --no-obfuscate-namespace-name --no-obfuscate-label-name";
    $tarname = empty($tarname) ? "{$encryptDevDir}.{$affix}.des3" : $tarname;
    exec($command);
    $command = "mv {$encryptDir} {$encryptDevDir}";
    exec($command);

    $command = "tar -zcvf - {$encryptDevDir}|openssl des3 -salt -k {$password} | dd of={$tarname}";
    exec($command);
    $command = "mv {$tarname} appcodes";
    exec($command);

    $command = "cp -Rf {$target}/yakpro-po/obfuscated {$encryptDir}";
    exec($command);
}

function decrypt($password, $tarname)
{
    global $encryptDir, $encryptDevDir,  $target;
    $command = "rm -rf {$encryptDir} {$encryptDevDir} {$target}";
    exec($command);

    $command = "cp appcodes/{$tarname} .";
    exec($command);

    $command = "dd if={$tarname} |openssl des3 -d -k {$password}|tar zxf -";
    exec($command);

    $command = "rm -rf {$tarname}";
    exec($command);

    $command = "mv {$encryptDevDir} {$encryptDir}";
    exec($command);
}
