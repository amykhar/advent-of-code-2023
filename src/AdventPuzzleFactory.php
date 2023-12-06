<?php

namespace App;

use App\Utils\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class AdventPuzzleFactory
{
    public static function create(
        OutputInterface $output,
        string $inputFile,
        string $mode,
        Logger $logger,
        string $day,
        int $part = 1
    ): AdventPuzzle
    {
        $className = "App\\Day$day\\Day$day";
        return new $className(
            $output,
            $inputFile,
            $mode,
            $logger,
            $part
        );
    }

}