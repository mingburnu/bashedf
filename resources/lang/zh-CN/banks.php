<?php
$banks = [];
$lines = explode(PHP_EOL, Curl::to('https://raw.githubusercontent.com/hexindai/bcbc/master/data/name.csv')->get());
foreach ($lines as $i => $line) {
    if ($i === 0 || mb_strlen($line) === 0) {
        continue;
    }

    $kv_pair = explode(',', $line);
    $banks[$kv_pair[0]] = $kv_pair[1];
}
return $banks;