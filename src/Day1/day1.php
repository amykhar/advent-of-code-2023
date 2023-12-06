<?php

require '../Utils/readFile.php';
require '../Utils/log.php';
function parse($string) {
    $parsed = [];
    $digitsAsWords = [
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
    ];

    $digits = [
        'one' => null,
        'two' => null,
        'three' => null,
        'four' => null,
        'five' => null,
        'six' => null,
        'seven' => null,
        'eight' => null,
        'nine' => null,
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
    ];

    $parsed = [];

    $keys = array_keys($digits);

    foreach ($keys as $digit) {

        $test = preg_match_all("/$digit/", $string, $matches, PREG_OFFSET_CAPTURE);
        if ($test) {
            $digits[$digit] = $matches;
        }
    }

    foreach($digits as $key => $value) {
        if (!is_numeric($key)) {
            $key = $digitsAsWords[$key];
        }
        if ($value !== null) {
            foreach($value as $match) {
                foreach($match as $match2) {
                    $index = (int)$match2[1];
                    $parsed[$index] = $key;
                }
            }
        }
    }
    ksort($parsed);
    return $parsed;
}

function getTwoDigitNumberFromArray($array, $logFile) {
    $length = count($array);
    if ($length === 1) {
        $digit = array_pop($array);
        $num =  ($digit * 10) + $digit;
        writeToLog ($logFile,'Two Digit Number: ' . $num . "\n");
        return $num;
    }

    $secondDigit =  array_pop($array);
    $reversed = array_reverse($array);
    $firstDigit = array_pop($reversed);
    $num = ($firstDigit * 10) + $secondDigit;
    writeToLog ($logFile, 'Two Digit Number: ' . $num . "\n");
    return $num;
}
////////////////////////
$logName = 'day1_log.txt';
$logHandle = createLogFile($logName);

$test = false;
if (count($argv) > 1) {
    $test = $argv[1];
}

$filename = 'day1_input.txt';
if ($test) {
    $filename = 'day1_test.txt';
}
writeToLog($logHandle, "Filename: " . $filename . "\n");
$data = readFileIntoArray($filename);
$sum = 0;
foreach ($data as $key => $line) {
    writeToLog($logHandle, "Line Number: " . $key . "\n");
    writeToLog($logHandle,"String : " . $line . "\n");
    $digits = parse($line);
    $sum += getTwoDigitNumberFromArray($digits, $logHandle);
    writeToLog($logHandle,"Current Sum : " . $sum . "\n");
}

writeToLog($logHandle,"Total: " . $sum . "\n");
closeLogFile($logHandle);

