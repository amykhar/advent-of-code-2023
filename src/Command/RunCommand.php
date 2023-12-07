<?php

namespace App\Command;

use App\AdventPuzzleFactory;
use App\Utils\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:run',
    description: 'Run the puzzle code for the day',
    hidden: false
)]
class RunCommand extends Command
{
    protected static string $name = 'run';
    protected static string $defaultDescription = 'Advent of Code 2023 Runner';

    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Advent of Code 2023 Runner')
            // the command help shown when running the command with the "--help" option
            ->setHelp('Usage: run --day 7 --test --part 1')
            ->addArgument('day', InputArgument::REQUIRED)
            ->addOption('test', 't')
            ->addOption('part', 'p', InputOption::VALUE_REQUIRED, )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $day = $input->getArgument('day');
        $mode = $input->getOption('test') ? 'test' :  '';
        $part = $input->getOption('part') ?? 1;

        $output->write(
            "<comment>Advent of Code 2023 Day $day</comment>",
            true
        );

        $logFileName = dirname(__DIR__,2).'/logs/day' . $day . '_log.txt';
        $inputFileName = dirname(__DIR__, 2). '/data/day' . $day . '_input.txt';
        $logger = new Logger($logFileName);

        $puzzle = AdventPuzzleFactory::create(
            $output,
            $inputFileName,
            $mode,
            $logger,
            $day,
            $part
        );

        $puzzle->solve();

        return Command::SUCCESS;
    }

}