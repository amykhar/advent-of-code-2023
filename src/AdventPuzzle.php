<?php

namespace App;

use App\Utils\Logger;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AdventPuzzle
{
    private array $puzzleData;

    private mixed $answer;

    public function __construct(
        private OutputInterface $output,
        private string $inputFile,
        protected string $mode,
        protected Logger $logger,
        protected int $part = 1
    ) {
        if ($this->mode === 'test') {
            $this->inputFile = str_replace(
                '_input.txt',
                '_test.txt',
                $this->inputFile
            );
        }
        $this->readFileIntoArray();
    }

    public function getAnswer(): mixed
    {
        return $this->answer;
    }

    public function setAnswer(mixed $answer): void
    {
        $this->answer = $answer;
    }

    public function getPuzzleData(): array
    {
        return $this->puzzleData;
    }

    public function output(string $message, string $tag = 'info'): void
    {
        $message = "<$tag>$message</$tag>";
        $this->output->write($message, true);
    }

    public function reportAnswer(): void
    {
        $this->output("Answer: " . $this->getAnswer());
    }

    abstract public function solve(): void;

    protected function breakAndTrim(string $data): array
    {
        return preg_split('/\s+/', trim($data));
    }

    protected function getRawData(string $data, string $key): array|false
    {
        $stripped = str_replace($key, '', $data);
        return preg_split('/\s+/', trim($stripped));
    }

    protected function log($message): void
    {
        $this->logger->log($message);
    }

    protected function stringToInt(string $data): ?int
    {
        $data = trim($data);
        if ($data) {
            return (int)$data;
        }
        return null;
    }

    private function readFileIntoArray(): void
    {
        $file = fopen($this->inputFile, "r");
        $this->puzzleData = [];
        while (($line = fgets($file)) !== false) {
            $this->puzzleData[] = $line;
        }
        fclose($file);
    }
}