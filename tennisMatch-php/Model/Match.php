<?php

require_once 'PlayerInterface.php';

class Match
{
    //a match has two players
    private   $playerA;
    private   $playerB;

    //match name
    private  $name;

    //store the initial points
    private $points = [];

    //analyze initial points one by one, and come out result, ie: game | advantage | deuce | scores: 0 - 15, 0 - 30, 0 - 40
    private $scores = [];

    //store the match results, when empty, match in ongoing, when results have elements, match is over
    private $results = [];


    public function __construct($name)
    {
        $this->name = $name;
    }

    public  function getName() {
        return $this->name;
    }

    public function getPoints() {
        return $this->points;
    }

    public function addPoints($point) {
        array_push($this->points, $point);
    }

    public function getPlayerA() {
        return $this->playerA;
    }

    public function setPlayerA(PlayerInterface $playerA) {
        $this->playerA = $playerA;
    }

    public function getPlayerB() {
        return $this->playerB;
    }

    public function setPlayerB(PlayerInterface $playerB) {
        $this->playerB = $playerB;
    }

    public function getScores() {
        return $this->scores;
    }

    public function setResults($results) {
        $this->results = $results;
    }
    public function getResults() {
        return $this->results;
    }

    /**
     * 1.without element, return initData
     * 2.last score result is game, return null for restart calculate score
     * 3. other situation return the end of element.
     */
    public function getLastScore() {
        $initData = [
            'scoreA' => 0, // points value 0, 1, 2, 3, 4, 5 ...
            'scoreB' => 0, // points value 0, 1, 2, 3, 4, 5 ...
            'result' => '' //game | advantage | deuce | scores: 0 - 15, 0 - 30, 0 - 40
        ];
        if(count($this->scores) == 0 ) return $initData;

        $lastScore = end($this->scores);
        if($lastScore['result'] == 'game') return $initData;
        return $lastScore;
    }

    public function addScore($score) {
        array_push($this->scores,$score);
    }

}
