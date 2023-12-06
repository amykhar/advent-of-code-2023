<?php
require '../Utils/readFile.php';

function parseLine(string $line): array
{
    $gameId = getGameId($line);
    $guesses = explode(';', $line);
    $data = ['game_id' => $gameId, 'guesses' => []];
    foreach ($guesses as $guess) {
        $blue = getTotalForColor($guess, 'blue');
        $red = getTotalForColor($guess, 'red');
        $green = getTotalForColor($guess, 'green');
        $data['guesses'][] = ['blue' => $blue, 'red' => $red, 'green' => $green];

    }

    return $data;
}

function getGameId(string $line): int
{
    preg_match('/Game (\d+):/', $line, $output_array);
    return (int)$output_array[1];
}

function getTotalForColor(string $line, string $color): int
{
    $pattern = '/(\d+) ' . $color . '/';
    preg_match_all($pattern, $line, $output_array);
    return array_sum($output_array[1]);
}

function getValidGames($lines): void
{
    $total = 0;
    $red = 12;
    $green = 13;
    $blue = 14;

    foreach ($lines as $line) {
        $parsed = parseLine($line);
        $valid = true;
        foreach ($parsed['guesses'] as $guess) {
            if ($guess['blue'] > $blue || $guess['red'] > $red || $guess['green'] > $green) {
                $valid = false;
                break;
            }
        }
        if ($valid) {
            $total += $parsed['game_id'];
        }
    }

    echo("Total Valid: $total \n");
}

function getMinForColor(array $parsed, string $color): int
{
    $min = 0;
    foreach ($parsed['guesses'] as $guess) {
        if ($guess[$color] > $min) {
            $min = $guess[$color];
        }
    }
    return $min;
}

function getGamePower($lines): void
{
    $total = 0;
    foreach ($lines as $line) {
        $parsed = parseLine($line);
        $power = getMinForColor($parsed, 'blue') *
            getMinForColor($parsed, 'red') *
            getMinForColor($parsed, 'green');
        $total += $power;
    }

    echo "Total Power: $total \n";
}

$lines = readFileIntoArray('day2_input.txt');
getValidGames($lines);
getGamePower($lines);