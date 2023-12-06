<?php

require '../Utils/readFile.php';
require '../Utils/log.php';
require '../Utils/sorting.php';

function parseCards(array $data): array
{
    $cards = [];

    foreach ($data as $cardData) {
        $card = [];
        $rawRecord = explode(':', trim($cardData));
        $cardNumData = explode(' ', $rawRecord[0]);
        $cardNumber = trim(array_pop($cardNumData));
        $card['CardNumber'] = $cardNumber;
        if (array_key_exists($cardNumber, $cards)) {
            $cards[$cardNumber]['numCopies'] += 1;
        }
        $rawNumbers = explode('|', trim($rawRecord[1]));
        $winningNumbers = explode(' ', trim($rawNumbers[0]));
        $myNumbers = explode(' ', trim($rawNumbers[1]));
        $card['winningNumbers'] = $winningNumbers;
        $card['myNumbers'] = $myNumbers;
        $card['numCopies'] = 1;
        $cards[$cardNumber] = $card;
    }

    return $cards;
}

function checkCard(array $card, $logFile): int
{
    $winningNumbers = $card['winningNumbers'];
    $myNumbers = $card['myNumbers'];
    $matches = [];
    $winnings = 0;

    foreach ($winningNumbers as $winningNumber) {
        $checkNumber = trim($winningNumber);
        if ($checkNumber == '') {
            continue;
        }

        writeToLog($logFile, "Checking winning number: " . $checkNumber . "\n");
        if (in_array($checkNumber, $myNumbers)) {
            writeToLog($logFile, "Matched winning number: " . $winningNumber . "\n");
            $matches[] = $winningNumber;
        }
    }

    $numMatched = count($matches);
    writeToLog($logFile, "Number of matches: " . $numMatched . "\n");
    if ($numMatched > 0) {
        $winnings = (pow(2, $numMatched - 1));
    }
    writeToLog($logFile, "Winnings: " . $winnings . "\n");
    return ($winnings);
}

function getWinCountForCard($winningNumbers, $myNumbers)
{
    $winCount = 0;
    foreach ($winningNumbers as $winningNumber) {
        $checkNumber = trim($winningNumber);
        if ($checkNumber == '') {
            continue;
        }
        if (in_array($checkNumber, $myNumbers)) {
            $winCount++;
        }
    }

    return $winCount;
}

function checkCard2(
    array &$allCards,
    int $cardNumber,
    $highestCard,
    $status,
    $logFile
): bool
{
    if (!array_key_exists($cardNumber, $allCards)) {
        return false;
    }
    $card = $allCards[$cardNumber];
    writeToLog($logFile, "Checking card: " . $cardNumber . "\n");
    $nextCardNumber = $cardNumber + 1;
    $howManyCopies = $card['numCopies'];
    writeToLog($logFile, "How many copies: " . $howManyCopies . "\n");
    $winningNumbers = $card['winningNumbers'];
    $myNumbers = $card['myNumbers'];

    $winCount = getWinCountForCard($winningNumbers, $myNumbers);
    writeToLog($logFile, "Win count: " . $winCount . "\n");
    for ($i = 1; $i <= $winCount; $i++) {
        $newCardNumber = $cardNumber + $i;
        if ($newCardNumber > $highestCard) {
            return false;
        }
        writeToLog($logFile, "Adding " . $howManyCopies . " copies to card number " . ($newCardNumber) . "\n");
        $allCards[$newCardNumber]['numCopies'] += (1 * $howManyCopies);
        writeToLog($logFile, "Card  " . $newCardNumber . " now has " . $allCards[$newCardNumber]['numCopies'] . "\n");
    }
    if ($nextCardNumber <= $highestCard) {
        if($nextCardNumber && $status) {
            $status = checkCard2(
                $allCards,
                $nextCardNumber,
                $highestCard,
                $status,
                $logFile
            );
        }
    }

    return $status;
}


/////////////////////////////////////
$logName = 'day4_log.txt';
$logHandle = createLogFile($logName);

$test = false;
if (count($argv) > 1) {
    $test = $argv[1];
}

$filename = 'day4_input.txt';
if ($test) {
    $filename = 'day4_test.txt';
}
writeToLog($logHandle, "Filename: " . $filename . "\n");
$data = readFileIntoArray($filename);
$cards = parseCards($data);
$winnings = 0;
/**foreach ($cards as $card) {
 * $moneyWon = checkCard($card, $logHandle);
 * $winnings += $moneyWon;
 * }
 *
 * echo "Part One: You won $" . $winnings . "\n";
 */
ksort($cards, SORT_ASC);
$highestCard = array_pop($cards);
$highestCardNumber = $highestCard['CardNumber'];
$cards[$highestCardNumber] = $highestCard;
writeToLog($logHandle, "Highest card number: " . $highestCardNumber . "\n");
$status = true;

while ($status === true) {
    $status = checkCard2(
        $cards,
        1,
        $highestCard,
        $status,
        $logHandle
    );
}

$totalCards = 0;
foreach ($cards as $card) {
    $totalCards += $card['numCopies'];
}

echo "Part Two: You won " . $totalCards . "\n";