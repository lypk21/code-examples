<?php

class Player implements PlayerInterface
{
    private $name;
    private $winGames = 0; //the number of win games in a tournament
    private $lostGames = 0; //the number of lost games in a tournament
    private $sets = 0; //the number of set in a match, reset to 0 when a match finish
    private $games = 0; //the number of win games in a set, up to 6, reset to 0 when a set finish

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getWinGames() {
        return $this->winGames;
    }

    public function getLostGames() {
        return $this->lostGames;
    }

    public function getSets() {
        return $this->sets;
    }

    public function getGames() {
        return $this->games;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /*
     * when win game, games add 1 and winGames add 1
     */
    public function winGame() {
        $this->games++;
        $this->winGames++;
    }

    /*
     * when lose game, loseGames add 1
     */
    public function lostGame() {
        $this->lostGames++;
    }

    /*
     * when win 6 games, add 1 set
     */
    public function addSet() {
        $this->sets++;
    }

    /*
     * reset to 0 when a set finish
     */
    public function clearGames() {
        $this->games = 0;
    }

    /*
     * reset to 0 when a match finish
     */
    public function clearSets() {
        $this->sets = 0;
    }

    /**
     * get the result report in a tournament for a player
     * @return string
     */
    public function getReportInTournament() {
       return $this->getWinGames(). '  '.$this->getLostGames();
    }
}
