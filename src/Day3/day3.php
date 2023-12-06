<?php
require '../Utils/readFile.php';
function createSchematicGrid($schematicData): array
{
    $grid = array();
    foreach ($schematicData as $row => $schematicRow) {
        $grid[$row] = [];
        $rowArray = str_split(trim($schematicRow));
        $numColumns = count($rowArray);
        for ($i = 0; $i < $numColumns; $i++) {
            $value = null;
            $potential = $rowArray[$i];

            if (is_numeric($potential)) {
                $value = $potential;
            } elseif ($potential === "*") {
                $value = "POSSIBLE_GEAR";
            } elseif ($potential === ".") {
                $value = ".";
            } else {
                $value = null;
            }
            $grid[$row][$i] = $value;
        }
    }


    return $grid;
}

function testCandidate($candidate, $row, $column, $grid): bool
{
    $rowLength = count($grid[$row]);
    $gridHeight = count($grid);
    $candidateLength = strlen($candidate);
    $columnBefore = $column - 1 > 0 ? $column - 1 : $column;
    $columnAfter = $column + $candidateLength + 1 < $rowLength ? $column + $candidateLength : $column + $candidateLength - 1;
    $rowBefore = $row - 1 >= 0 ? $row - 1 : null;
    $rowAfter = $row + 1 < $gridHeight ? $row + 1 : null;

    if ($columnBefore !== $column && ($grid[$row][$columnBefore] === null || $grid[$row][$columnBefore] === "POSSIBLE_GEAR")) {
        return true;
    }
    if ($columnAfter !== $column + $candidateLength - 1 && ($grid[$row][$columnAfter] === null || $grid[$row][$columnAfter] === "POSSIBLE_GEAR")) {
        return true;
    }

    if ($rowBefore !== null) {
        for ($i = $columnBefore; $i <= $columnAfter; $i++) {
            if (null === $grid[$rowBefore][$i]) {
                return true;
            }
            if ("POSSIBLE_GEAR" === $grid[$rowBefore][$i]) {
                return true;
            }
        }
    }

    if ($rowAfter !== null) {
        for ($i = $columnBefore; $i <= $columnAfter; $i++) {
            if (null === $grid[$rowAfter][$i]) {
                return true;
            }
            if ("POSSIBLE_GEAR" === $grid[$rowAfter][$i]) {
                return true;
            }
        }
    }

    return false;
}

function sumPartNumbers($grid, $numRows, $numColumns, &$gearGrid, &$partNumberGrid)
{
    $sum = 0;
    $gearGrid = [];
    for ($row = 0; $row < $numRows; $row++) {
        $potentialPartNumber = '';
        $startIndex = 0;
        for ($column = 0; $column < $numColumns; $column++) {
            $columnValue = $grid[$row][$column];
            $checkNumber = false;
            if ($columnValue === "POSSIBLE_GEAR") {
                $checkNumber = true;
                $columnValue = '*';
                if (!array_key_exists($row, $gearGrid)) {
                    $gearGrid[$row] = [];
                }
                if (!array_key_exists($column, $gearGrid[$row])) {
                    $gearGrid[$row][$column] = [];
                }

                $gearGrid[$row][$column] = [];
            } else {
                if ($columnValue === ".") {
                    $checkNumber = true;
                } else {
                    if (is_numeric($columnValue)) {
                        if ($potentialPartNumber === '') {
                            $startIndex = $column;
                        }
                        $potentialPartNumber .= $columnValue;
                    } else {
                        $checkNumber = true;
                    }
                }
            }
            if ($checkNumber && $potentialPartNumber !== '') {
                $isPartNumber = testCandidate($potentialPartNumber, $row, $startIndex, $grid);
                if ($isPartNumber) {
                    $sum += $potentialPartNumber;
                    if(!array_key_exists($potentialPartNumber, $partNumberGrid)) {
                        $partNumberGrid[$potentialPartNumber] = [];
                    }
                    $partNumberGrid[$potentialPartNumber][] = [$row,$startIndex, $column ];
                }
                $potentialPartNumber = '';
            }
        }// End for column
        if ($potentialPartNumber !== '') {
            $isPartNumber = testCandidate($potentialPartNumber, $row, $startIndex, $grid);
            if ($isPartNumber) {
                $sum += $potentialPartNumber;
                if(!array_key_exists($potentialPartNumber, $partNumberGrid)) {
                    $partNumberGrid[$potentialPartNumber] = [];
                }
                $partNumberGrid[$potentialPartNumber][] = [$row,$startIndex, $column ];
            }
        }
    } // End for row

    return $sum;
}

function sumGearRatio($gearGrid, $partNumberGrid): int
{
    $sum = 0;
    $adjacent = [];
    foreach ($partNumberGrid as $partNumber => $partNumberArray) {
        foreach($partNumberArray as $coordinates) {
            $row = $coordinates[0];
            $rowBefore = $row - 1;
            $rowAfter = $row + 1;

            $startIndex = $coordinates[1];
            $beforeStart = $startIndex - 1;

            $endIndex = $coordinates[2];
            $afterEnd = $endIndex;

            if (array_key_exists($rowBefore, $gearGrid)) {
                if (!array_key_exists($rowBefore, $adjacent)) {
                    $adjacent[$rowBefore] = [];
                }
                foreach ($gearGrid[$rowBefore] as $gearColumn => $data) {
                    if (!array_key_exists($gearColumn, $adjacent[$rowBefore])) {
                        $adjacent[$rowBefore][$gearColumn] = [];
                    }
                    if ($gearColumn >= $beforeStart && $gearColumn <= $afterEnd) {
                        $adjacent[$rowBefore][$gearColumn][] = $partNumber;
                    }
                }
            }
            if (array_key_exists($row, $gearGrid)) {
                if (!array_key_exists($row, $adjacent)) {
                    $adjacent[$row] = [];
                }

                foreach ($gearGrid[$row] as $gearColumn => $data) {
                    if (!array_key_exists($gearColumn, $adjacent[$row])) {
                        $adjacent[$row][$gearColumn] = [];
                    }
                    if ($gearColumn >= $beforeStart && $gearColumn <= $afterEnd) {
                        $adjacent[$row][$gearColumn][] = $partNumber;
                    }
                }
            }

            if (array_key_exists($rowAfter, $gearGrid)) {
                if (!array_key_exists($rowAfter, $adjacent)) {
                    $adjacent[$rowAfter] = [];
                }

                foreach ($gearGrid[$rowAfter] as $gearColumn => $data) {
                    if (!array_key_exists($gearColumn, $adjacent[$rowAfter])) {
                        $adjacent[$rowAfter][$gearColumn] = [];
                    }
                    if ($gearColumn >= $beforeStart && $gearColumn <= $afterEnd) {
                        $adjacent[$rowAfter][$gearColumn][] = $partNumber;
                    }
                }
            }
        }
    }
    foreach ($adjacent as $row => $potentialGear) {
        foreach ($potentialGear as $column => $gear) {
            if (count($gear) === 2) {
                $sum += ($gear[0] * $gear[1]);
            }
        }
    }

    return $sum;
}

$data = readFileIntoArray("day3_input.txt");
$grid = createSchematicGrid($data);
$gearGrid = [];
$partNumberGrid = [];
$sum = sumPartNumbers($grid, count($data), strlen(trim($data[0])), $gearGrid, $partNumberGrid);
echo "The sum of all part numbers is: $sum\n";
$gearRatioSum = sumGearRatio($gearGrid, $partNumberGrid);
echo "The sum of all gear ratios is: $gearRatioSum\n";
