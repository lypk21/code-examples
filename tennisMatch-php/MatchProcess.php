<?php


class MatchProcess {
    //point match score
    const POINT_TO_SCORE = [0=>0, 1=>15, 2=>30, 3=>40];
    const GAMES_WIN_FOR_SET = 6;
    const SET_WIN_FOR_MATCH = 2;
    const GAME_AHEAD_POINT = 2;
    const ADVANTAGE_AHEAD_POINT = 2;
    const DEUCE_AHEAD_POINT = 0;
    const WIN_GAME_POINT = 4;

    public function analyzePointAndStoreResultToMatch($match,$point) {
        $lastScore = $match->getLastScore();
        $score = $this->analyzeScoreByPoint($lastScore,$point);
        $match->addScore($score);
        //game point
        if($score['result'] === 'game') {
            //analyze games for players and statistic their performance
            $winner = $this->analyzeGameAndStatPlayers($match,$point);
            if($winner->getSets() === self::SET_WIN_FOR_MATCH) {
                $this->storeMatchResult($match,$point);
            }
        }
    }

    /**
     * when, match get 'game', calculate player games, sets, winGames, lostGames
     * 1.winner add 1 game but reset to 0 when reach 6 games
     * 2.winner add 1 winGame during the  tournament
     * 3.loser add 1 $lostGame during the  tournament
     * 4.when reach 6 games, winner add 1 set, will reset to 0 when match over
     * @param $point
     * @return  winner
     */
    private function analyzeGameAndStatPlayers($match,$point) {
        $winner = $this->getWinnerByPoint($match,$point);
        $loser = $this->getLoserByPoint($match,$point);
        $winner->winGame();  // when win game, games add 1 and winGames add 1
        $loser->lostGame();  // when lose game, loseGames add 1
        if($winner->getGames() == self::GAMES_WIN_FOR_SET) {
            $winner->addSet(); //when win 6 games, add 1 set
            $winner->clearGames(); //reset winner games to 0
            $loser->clearGames(); //reset loser games to 0
        }
        return $winner;
    }

    /**
     * calculate score by point, get score according to tennis rules:
     * 1.winner add 1 points, if under 4, result is score
     * 2.when winner >= 4 if ahead 2 point, result is game
     * 3.when winner >= 4 if ahead 1 point, result is advantage
     * 4.when winner >= 4 if ahead 0 point, result is deuce
     */
    private function analyzeScoreByPoint($lastScore, $point) {
        $score = $lastScore;
        //decide which side is winner
        if($point == 0) {
            $winner = 'scoreA';
            $loser = 'scoreB';
        } else {
            $winner = 'scoreB';
            $loser = 'scoreA';
        }
        $score[$winner] = $lastScore[$winner] + 1;  // winner add 1 point

        if($score[$winner] < self::WIN_GAME_POINT)
            $score['result'] = self::POINT_TO_SCORE[$score['scoreA']]. ' - ' . self::POINT_TO_SCORE[$score['scoreB']]; //if winner point under 4, result is score
        else {
            $aheadPoint = $score[$winner] - $score[$loser]; // winner ahead points
            if($aheadPoint >= self::GAME_AHEAD_POINT) $score['result'] = 'game'; //ahead 2 points, game
            elseif ($aheadPoint == self::ADVANTAGE_AHEAD_POINT) $score['result'] = 'advantage'; //ahead 1 points, advantage
            elseif($aheadPoint == self::DEUCE_AHEAD_POINT) $score['result'] = 'deuce'; //ahead 0 points, deuce
        }
        return $score;
    }


    /**
     * when match is over,
     * 1. store match result data
     * 2. store match report
     * 3. reset sets to 0 for players
     */
    private function storeMatchResult($match,$point) {
        $winner = $this->getWinnerByPoint($match,$point);
        $loser = $this->getLoserByPoint($match,$point);
        //store match result data
        $result['winner'] = $winner->getName();
        $result['loser'] = $loser->getName();
        $result['winnerSets'] = $winner->getSets();
        $result['loserSets'] = $loser->getSets();
        //store match report
        $result['report1'] = 'Person '.$result['winner']. ' defeated Person '.$result['loser'];
        $result['report2'] =  $result['winnerSets']. ' sets to '.$result['loserSets'];
        //reset sets to 0 for players
        $winner->clearSets();
        $loser->clearSets();
        $match->setResults($result);
    }

    private function getWinnerByPoint($match,$point) {
        if($point == 0) {
            $winner = $match->getPlayerA();
        } else {
            $winner = $match->getPlayerB();
        }
        return $winner;
    }

    private function getLoserByPoint($match, $point) {
        if($point == 0) {
            $loser = $match->getPlayerB();
        } else {
            $loser = $match->getPlayerA();
        }
        return $loser;
    }

}
