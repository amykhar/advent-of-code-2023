<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:setup',
    description: 'Create the directory structure for a new puzzle',
    hidden: false
)]
class SetupPuzzleCommand extends Command
{
    protected static string $name = 'setup';
    protected static string $defaultDescription = 'Advent of Code 2023 Next Day Setup';

    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Advent of Code 2023 Setup')
            // the command help shown when running the command with the "--help" option
            ->setHelp('Usage: app:setup')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $output->write(
            "<comment>Setting Up the Code Files</comment>",
            true
        );

        $this->createDailyDirectory();

        return Command::SUCCESS;
    }

    private function getPuzzlePath($year, $day): string
    {
        $basePath = dirname(__DIR__, 2);
        return $basePath . "/src/Day{$day}/";
    }

    private function getContentFromTemplate($className): string
    {
        $filename = dirname(__DIR__, 2) . '/templates/class-template.txt';
        $content = file_get_contents($filename);
        $content = str_replace('CLASSNAME', $className, $content);
        return $content;
    }

    private function createDailyDirectory(): void
    {
        $year = date("Y");
        $day = date("j") + 1;

        $path = $this->getPuzzlePath($year, $day);

        if (is_dir($path)) {
            return;
        }

        // Create base directory
        mkdir($path, 0777, true);

        $classFileName = "Day{$day}.php";
        $className = "Day{$day}";
        $content = $this->getContentFromTemplate($className);

        // Create each file with default content
        $files = [
            "puzzle.md" => "# Day {$day}",
             $classFileName => $content,
            'notes.txt' => "Part1: \nPart 2: \n",
        ];

        foreach ($files as $file => $content) {
            file_put_contents($path . "/" . $file, $content);
        }
    }
}