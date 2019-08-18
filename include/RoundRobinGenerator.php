<?php

/**
 * @param array $teamList
 * @return array
 * Made to check if all matchs were generated
 */
function generateAllPossibleMatchs(array $teamList) {
    $pos = 1;
    $matchs = [];
    foreach ($teamList as $team) {
        for ($i = $pos; $i < count($teamList); $i++) {
            if ($i % 2 == 0) {
                array_push($matchs, [$team, $teamList[$i]]);
            } else {
                array_push($matchs, [$teamList[$i], $team]);
            }

        }
        $pos++;
    }
    return $matchs;
}


/**
 * @param $teamList
 * @return array
 * Generate all matchs for a Pool without a team playing twice in a row
 */
function recursiveMatchGenerator(array $teamList) {
    $matchs = array();
    if (count($teamList) % 2 == 0) { // Pair
        for ($i = 0; $i < count($teamList); $i += 2) {
            if (isset($teamList[$i + 1])) {
                $matchs[] = [$teamList[$i], $teamList[$i + 1]];
            }
        }

        $first  = array_shift($teamList);
        array_push($teamList, $first);

        for ($i = 0; $i < count($teamList); $i += 2) {
            if (isset($teamList[$i + 1])) {
                $matchs[] = [$teamList[$i], $teamList[$i + 1]];
            }
        }

        array_unshift($teamList, array_pop($teamList));

        for ($index = 2; $index <= round((count($teamList) / 2) + ( (count($teamList) - 4 ) / 2 )); $index ++){
            for ($i = 0; $i < count($teamList); $i ++) {
                if (isset($teamList[$i + $index])) {
                    $matchs[] = [$teamList[$i], $teamList[$i + $index]];
                }
            }
        }

    } else {
        for ($i = 0; $i < count($teamList); $i += 2) {
            if (isset($teamList[$i + 1])) {
                $matchs[] = [$teamList[$i], $teamList[$i + 1]];
            }
        }
        array_unshift($teamList, end($teamList)); // Add the last to the beginning

        for ($i = 0; $i < count($teamList); $i += 2) {
            if (isset($teamList[$i + 1])) {
                $matchs[] = [$teamList[$i], $teamList[$i + 1]];
            }
        }

        array_shift($teamList); // Remove the first (Copied from last position)

        for ($index = 2; $index <= round((count($teamList) / 2) + ( (count($teamList) -5 ) / 2 )); $index ++){
            for ($i = 0; $i < count($teamList); $i ++) {
                if (isset($teamList[$i + $index])) {
                    $matchs[] = [$teamList[$i], $teamList[$i + $index]];
                }
            }
        }

    }
    return $matchs;
}
