#!/usr/bin/env php
<?php
use Kennynguyeenx\FacebookSticker\FacebookSticker;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[5]) || !is_numeric($argv[5])) {
    exit('Missing number of frames');
}

if (!isset($argv[4]) || !is_numeric($argv[4])) {
    exit('Missing number of columns');
}

if (!isset($argv[3]) || !is_numeric($argv[3])) {
    exit('Missing number of rows');
}

if (!isset($argv[2])) {
    exit('Missing destination image path');
}

if (!isset($argv[1])) {
    exit('Missing source image path');
}

$facebookSticker = new FacebookSticker();

try {
    $facebookSticker->createAnimatedSticker($argv[1], $argv[2], intval($argv[3]), intval($argv[4]), intval($argv[5]));
} catch (Exception $exception) {
    exit($exception->getMessage());
}

exit('Done');