<?php
$file = 'calls_log.txt';
$current = file_get_contents($file);
$current .= date("Y-m-d H:i:s").";".$_GET[number].";".$_GET[type]."\r";
file_put_contents($file, $current);
?>