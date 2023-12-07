<?php

namespace App\Day6;

use App\AdventPuzzle;

/**
 * For each whole millisecond you spend at the beginning of the race holding down the button,
 * the boat's speed increases by one millimeter per millisecond.
 */
class Day6 extends AdventPuzzle
{
    private array $raceTimes = []; //time in milliseconds
    private array $winningDistances = []; //distance in millimeters

    public function solve(): void
    {
        $this->parseRaceData();
        if (2 === $this->part) {
            $this->preparePart2();
        }

        $this->run();
        $this->reportAnswer();
    }

    private function calculateWins($gameId): int
    {
        $index = $gameId - 1;
        $distance = $this->winningDistances[$index];
        $this->log("The distance is " . $distance);
        $time = $this->raceTimes[$index];
        $this->log("The time is " . $time);
        $x = ceil($time / 2);
        $y = floor($time / 2);
        $this->log("The ceiling is " . $x);
        $this->log("The floor is " . $y);
        $this->log("Multiple is " . $x * $y);
        $difference = ($x * $y) - $distance;
        $this->log("The difference is " . $difference);
        $value = $x * $y;
        $i = 1;
        while ($value >= $distance) {
            $value -= $i * 2;
            $i++;
        }

        $answer = 2 * ($i - 1);
        if ($x == $y) {
            $answer--;
        }

        return $answer;
    }



    private function parseRaceData(): void
    {
        $data = $this->getPuzzleData();
        $dirtyTimes = $this->getRawData($data[0], 'Time: ');
        foreach ($dirtyTimes as $dirtyTime) {
            $value = $this->stringToInt($dirtyTime);
            if ($value) {
                $this->raceTimes[] = $value;
            }
        }
        $dirtyDistances = $this->getRawData($data[1], 'Distance: ');
        $this->winningDistances = [];
        foreach ($dirtyDistances as $dirtyDistance) {
            $value = $this->stringToInt($dirtyDistance);
            if ($value) {
                $this->winningDistances[] = $value;
            }
        }
    }

    private function preparePart2(): void
    {
        $runTime = implode('', $this->raceTimes);
        $runDistance = implode('', $this->winningDistances);
        $this->raceTimes = [(int)$runTime];
        $this->winningDistances = [(int)$runDistance];
    }

    private function run(): void
    {
        $numWins = 1;
        $numRaces = count($this->raceTimes);
        $this->log("There are " . $numRaces . " races");

        for ($i = 1; $i <= $numRaces; $i++) {
            $numWins *= $this->calculateWins($i);
            $this->log("You won " . $numWins . " times");
        }

        $this->setAnswer($numWins);
    }


}