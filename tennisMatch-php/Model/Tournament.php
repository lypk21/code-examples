<?php


class Tournament
{
    //a tournament has multiple matches, a match is identified by its name
    private $matches = [];

    //a tournament has multiple players, a player is identified by his name
    private $players = [];

    private static $_instance;

    private function __construct()
    {
    }
    private function __clone()
    {
    }
    //singleton
    public static function getInstance() {
        if(!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function addMatch($match) {
        if(!empty($match->getName())) $this->matches[$match->getName()] = $match;
    }

    public function addPlayer($player) {
        if(!empty($player->getName())) $this->players[$player->getName()] = $player;
    }

    public function getMatchByName($matchName) {
        return !empty($this->matches[$matchName]) ? $this->matches[$matchName] : null;
    }

    public function getPlayerByName($playerName) {
        return !empty($this->players[$playerName]) ? $this->players[$playerName] : null;
    }

    public function getMatches() {
        return $this->matches;
    }

    public function getLastMatch() {
        return end($this->matches);
    }

    public function getPlayers() {
        return $this->players;
    }

}
