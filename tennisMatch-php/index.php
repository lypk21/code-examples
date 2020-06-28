<?php
include 'Application.php';


$application = new Application();

//read file from console and make file exist and readable
$application->inputAndvalidateFileFromConsole();

//analyze File line by line, and store the data in Tournament
$application->readLineByLineAndAnalyzeData();

$application->displayTournamentData();

$application->inputCommandAndOutputResult();








