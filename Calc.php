<?php
require_once('Connection.php');
require_once('Ikts.class.php');

/*
Ikts::addPlayer('Ali');
Ikts::addPlayer('Jayesh');
Ikts::addPlayer('Rohan');
Ikts::addPlayer('Nachiket');
Ikts::addPlayer('Deva');
Ikts::addPlayer('Vijendra');
Ikts::addPlayer('Prateek');
Ikts::addPlayer('Utkarsh');
Ikts::addPlayer('Murtuza');
*/

//Ikts::deletePlayer('abbas');

//Ikts::addFixture('Argentina','Bosnia',2);

//Ikts::deleteFixture(1);

//Ikts::getPredictions(13, 3, 1, 0);
//Ikts::getPredictions(6, 0, 2, 1);
//Ikts::getPredictions(11, 1, 0, 0);

//Ikts::evalRisk(8);

//Ikts::evalMain(8);

//Ikts::evalScore(8);

//Ikts::fillPlayer(8);

echo Ikts::deadline(10)."\n";
echo strtotime('now');


//Ikts::getGlobals();

//echo Ikts::$curr_round;
//echo Ikts::$feature_risk;


?>
