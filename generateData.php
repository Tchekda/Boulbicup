<?php

require_once "bootstrap.php";
require_once "RoundRobinGenerator.php";

$repos = ['Match', 'Team', 'Pool'];
foreach ($repos as $repo) {
    $repository = $entityManager->getRepository($repo);
    $entities = $repository->findAll();

    foreach ($entities as $entity) {
        $entityManager->remove($entity);
    }
    $entityManager->flush();
}

$teamList1 = ['ACBB 1', 'Amstel', 'Steatham', 'Neuilly', 'Dijon'];
//$teamList1 = ['Team A 1', 'Team A 2', 'Team A 3', 'Team A 4', 'Team A 5', 'Team A 6', 'Team A 7', 'Team A 8'];
$teamList2 = ['ACBB 2', 'HSV', 'Evry-Viry', 'Courbevoie', 'Bordeaux'];
//$teamList2 = ['Team B 1', 'Team B 2', 'Team B 3', 'Team B 4', 'Team B 5', 'Team B 6', 'Team B 7', 'Team B 8'];


$poolA = new Pool("A");
$entityManager->persist($poolA);


foreach ($teamList1 as $team) {
    $teamObject = new Team();
    $teamObject->setName($team);
    $teamObject->setPoints(0);
    $teamObject->setPool($poolA);
    $entityManager->persist($teamObject);
}

$poolB = new Pool("B");
$entityManager->persist($poolB);

foreach ($teamList2 as $team){
    $teamObject = new Team();
    $teamObject->setName($team);
    $teamObject->setPoints(0);
    $teamObject->setPool($poolB);
    $entityManager->persist($teamObject);
}


$entityManager->flush();
$matchsA = recursiveMatchGenerator($teamList1);
$matchsB = recursiveMatchGenerator($teamList2);
echo (count($matchsA) + count($matchsB)) . " Matchs sorted of "
    . (count(generateAllPossibleMatchs($teamList1)) + count(generateAllPossibleMatchs($teamList2))) . "\n\r";
$matchs = array();
array_map(function ($a, $b) use (&$matchs) { array_push($matchs, $a, $b); }, $matchsA, $matchsB);


$lastGameTime = new \DateTime();
$lastGameTime->setTimestamp(1557567000);

echo "Starting at : " . $lastGameTime->format('Y-m-d H:i') . "\n\r";

$matchObjects = array();

for ($i = 1; $i <= count($matchs); $i++){
    $match = $matchs[$i - 1 ];
    $gameTime = clone $lastGameTime;
    echo "New Match : " . $match[0] . ' vs ' . $match[1] . " at " . $gameTime->format('Y-m-d H:i') . "\n\r";
    $matchObject = new Match();
    $matchObject->setHost($entityManager->getRepository('Team')->findOneBy(array('name' => $match[0])));
    $matchObject->setAway($entityManager->getRepository('Team')->findOneBy(array('name' => $match[1])));
    $matchObject->setScore([0, 0]);
    $matchObject->setDatetime($gameTime);
    $entityManager->persist($matchObject);
    $lastGameTime->add(new DateInterval("PT35M"));
    if ( $lastGameTime->format('Hi') > "2030"){
        $lastGameTime->setTime(8, 00);
        date_add($lastGameTime, date_interval_create_from_date_string('1 days'));
    }else{
        if ($i % 4 == 0 and $i != 0)
            $lastGameTime->add(new DateInterval("PT15M")); //Mounir
    }



}

$entityManager->flush();