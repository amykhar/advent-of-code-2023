<?php

namespace App\Utils;

class Logger
{
    private $handle;
    private bool $onScreen = false;

    public function __construct(private readonly string $logFileName)
    {
        $this->createLog();
    }

    public function log($message): void
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        $logEntry = date('Y-m-d H:i:s') . ' log.php' . $message;
        fwrite($this->handle, $logEntry . PHP_EOL);
    }

    private function createLog(): void
    {
        if (file_exists($this->logFileName)){
            unlink($this->logFileName);
        }
        $this->handle = fopen($this->logFileName, "a");
    }

    public function closeLogFile(): void
    {
        fclose($this->handle);
    }
}