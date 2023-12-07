# Advent of Code 2023

## Setup for Next Day
The day before, run

```
bin/console app:setup
```
This will create the daily folder with the class file and a puzzle.md file

Unfortunately, there is still some manual setup necessary on the day of.
1. Get your puzzle input from the site and put it in a file called dayx_input.txt in the data folder, where x is the day number.
2. In the puzzle instructions, there will be some test data.  Copy that test data into dayx_test.txt in the data folder.

## Available Utilities
1. Logger  you can log messages with $this->log($message) within your puzzle class.  If you pass it an array, it will convert it to json
2. stringToInt(string $data): ?int -> takes a string and trims off all whitespace and returns the integer value
3. readFileIntoArray(): void - This is called automatically for you. It reads the test or normal input file into the puzzleData variable
4. getPuzzleData(): array - returns the puzzle data for you
5. output(string $message, string $tag = 'info'): void - writes colorful messages to the console
6. breakAndTrim(string $data) - takes a line of text, breaks it into an array and trims all the whitespace
7. setAnswer(mixed $answer): void - set your answer value when you know it
8. reportAnswer(): void - prints the answer value to the console for you

## Available protected class variables
$part = integer you can use for part specific logic
$logger = use for logging to a file

## Running your puzzle code and getting the answer
```
bin/console app:run {daynumber} {options}

```
Example for December 1:
```
bin/console app:run 1
```
Available options:
--part=x  It defaults to part 1.  For part 2:
```
bin/console app:run 1 --part=2
```
--test Defaults to off.  If you want to run using the test data do:
```
bin/console app:run 1 --test
```

Putting it all together:
```
bin/console app:run 1 --part=2 --test #Runs part 2 in test mode

```
This:
1. Loads your puzzle class with the logger and your input file
2. In your puzzle class, you need to put logic in the solve function to get your answer and report it.
3. You'll usually need to write a parsing function of some sort to get your data into structures you can use to solve.
