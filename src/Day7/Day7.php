<?php

namespace App\Day7;

use App\AdventPuzzle;

class Day7 extends AdventPuzzle
{
    private const array RANKED_CARDS = [
        'A' => 14,
        'K' => 13,
        'Q' => 12,
        'J' => 11,
        'T' => 10,
        9 => 9,
        8 => 8,
        7 => 7,
        6 => 6,
        5 => 5,
        4 => 4,
        3 => 3,
        2 => 2,
    ];

    private const array PART_2_RANKED_CARDS = [
        'A' => 14,
        'K' => 13,
        'Q' => 12,
        'T' => 10,
        9 => 9,
        8 => 8,
        7 => 7,
        6 => 6,
        5 => 5,
        4 => 4,
        3 => 3,
        2 => 2,
        'J' => 1,
    ];

    private const array HAND_TYPES = [
        'five_of_a_kind' => 10,
        'four_of_a_kind' => 9,
        'full_house' => 8,
        'three_of_a_kind' => 7,
        'two_pair' => 6,
        'one_pair' => 5,
        'high_card' => 4
    ];

    private array $hands = [];

    #[\Override] public function solve(): void
    {
        $this->parseInput();
        $this->log("There are " . count($this->hands) . " hands to evaluate");
        $this->setHandTypes();
        $this->sortByHandType();
        $answer = 0;
        foreach ($this->hands as $key => $hand) {
            $multiplier = $key + 1;
            $answer += $hand['bid'] * $multiplier;
        }

        $this->setAnswer($answer);
        $this->reportAnswer();
    }

    private function calculateHandType($hand): string
    {
        $hand = $this->sortHandByCardCount($hand);
        $highestCount = (array_pop($hand))['count'];
        $secondHighestCount = 0;
        if (5 !== $highestCount) {
            $secondHighestCount = (array_pop($hand))['count'];
        }
        return $this->getHandType($highestCount, $secondHighestCount);
    }

    private function calculateHandTypePart2($hand, $handString): string
    {
        $containsJoker = in_array('J', $handString);
        if ($containsJoker) {
            $this->log("The hand: " . implode('', $handString) . " contains a joker");
        }
        $hand = $this->sortHandByCardCount($hand);
        $bestCard = array_pop($hand);
        $highestCount = ($bestCard)['count'];
        $this->log("The hand: " . implode('', $handString) . " has $highestCount of the best card");
        $secondHighestCount = 0;
        if ($containsJoker) {
            if (array_key_exists('J', $hand)) {
                $numJokers = $hand['J']['count'];
                $highestCount += $numJokers;
                $this->log("The hand: " . implode('', $handString) . " has $numJokers jokers");
                $this->log("The hand: " . implode('', $handString) . " has $highestCount of the best card");
                unset($hand['J']);
            } else {
                $nextHighestCard = (array_pop($hand));
                if ($nextHighestCard) {
                    $nextHighestCardCount = $nextHighestCard['count'];
                } else {
                    return $this->getHandType(5, 0);
                }
                $highestCount += $nextHighestCardCount;
            }
        }
        if (5 !== $highestCount) {
            $secondHighestCount = (array_pop($hand))['count'];
        }

        return $this->getHandType($highestCount, $secondHighestCount);
    }

    private function getCardCounts($cards): array
    {
        $hand = [];
        foreach ($cards as $card) {
            if (!array_key_exists($card, $hand)) {
                $hand[$card] = [
                    'card_value' => 0,
                    'count' => 0,
                ];
            }
            $hand[$card]['count']++;
            $rank = self::RANKED_CARDS[$card];
            if (2 == $this->part) {
                $rank = self::PART_2_RANKED_CARDS[$card];
            }
            $hand[$card]['card_value'] = $rank;
        }

        return $hand;
    }

    /**
     * @param mixed $highestCount
     * @param array $hand
     * @return string
     */
    private function getHandType(int $highestCount, int $secondHighestCount): string
    {
        switch ($highestCount) {
            case 5:
                return 'five_of_a_kind';
            case 4:
                return 'four_of_a_kind';
            case 3:
                if ($secondHighestCount === 2) {
                    return 'full_house';
                } else {
                    return 'three_of_a_kind';
                }
            case 2:
                if ($secondHighestCount === 2) {
                    return 'two_pair';
                } else {
                    return 'one_pair';
                }
            default:
                return 'high_card';
        }
    }

    private function parseInput(): void
    {
        $data = $this->getPuzzleData();
        foreach ($data as $line) {
            $play = $this->parseLine($line);
            $hand = str_split($play[0]);
            $bid = $play[1];
            $countedHand = $this->getCardCounts($hand);
            $this->hands[] = [
                'hand' => $countedHand,
                'hand_string' => $hand,
                'bid' => $bid
            ];
        }
    }

    private function parseLine(string $line): array
    {
        return $this->breakAndTrim($line);
    }

    private function setHandTypes(): void
    {
        foreach ($this->hands as $key => $hand) {
            if (2 === $this->part) {
                $handType = $this->calculateHandTypePart2($hand['hand'], $hand['hand_string']);
            } else {
                $handType = $this->calculateHandType($hand['hand']);
            }
            $this->log("The hand: " . implode('', $hand['hand_string']) . " is a $handType");

            $this->hands[$key]['hand_type'] = self::HAND_TYPES[$handType];
        }
    }

    private function sortByHandType(): void
    {
        usort($this->hands, function ($a, $b) {
            if ($a['hand_type'] === $b['hand_type']) {
                $this->log("Same hand type");
                $this->log('Card A: ' . implode('', $a['hand_string']));
                $this->log('Card B: ' . implode('', $b['hand_string']));
                foreach ($a['hand_string'] as $key => $card) {
                    $aCard = $a['hand_string'][$key];
                    $bCard = $b['hand_string'][$key];
                    $aCardValue = $a['hand'][$aCard]['card_value'];
                    $bCardValue = $b['hand'][$bCard]['card_value'];
                    if ($aCardValue !== $bCardValue) {
                        return $aCardValue <=> $bCardValue;
                    }
                }
            }
            return $a['hand_type'] <=> $b['hand_type'];
        });
    }

    private function sortHandByCardCount($hand): array
    {
        uasort($hand, function ($a, $b) {
            return $a['count'] <=> $b['count'];
        });

        return $hand;
    }
}