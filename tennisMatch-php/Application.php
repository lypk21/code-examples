<?php
include 'Model/Tournament.php';
include 'Model/Match.php';
include 'Model/Player.php';

class Application
{
    private $file;
    private $lineCount = 0; //tracing the error for line number
    private $tournament;

    //point match score
    const POINT_TO_SCORE = [0=>0, 1=>15, 2=>30, 3=>40];
    const GAMES_WIN_FOR_SET = 6;
    const SET_WIN_FOR_MATCH = 2;

    public function inputAndvalidateFileFromConsole() {
        $file = readline('Enter a source file: ');
        while(!file_exists($file)) {
            echo "the file you input is not existed. \n";
            $file = readline('Enter a source file: ');
        }
        $this->file = $file;
    }

    public function getTournament() {
        //create singleton for Tournament, make sure only one instance during the life cycle of application
        if(empty($this->tournament)) $this->tournament = Tournament::getInstance();
        return $this->tournament;
    }

    public function readLineByLineAndAnalyzeData() {
        $file = fopen( $this->file, "r" ) or die("Couldn't open  $this->file");
        //read file line by line
        while(! feof($file))
        {
            $this->lineCount++;
            $line =  trim(fgets($file));
            //ignore empty $line
            if($line != '') {
                //1.Match: 01, filter out 'match' string, case-insensitive, then init Match Class
                $lineLowerStr = strtolower($line);
                if(strpos($lineLowerStr,'match') !== false) {
                    $this->filterLineAndInitMatch($line);
                }
                //2. when Person A vs Person B, filter out Player by 'vs' string after generate Match object
                if(strpos($lineLowerStr,'vs') !== false) {
                    $this->filterLineAndInitPlayers($line);
                }
                //3.when 0 or 1, filter out points for two players after generate Match  and Player object
                if(in_array(trim($line),[0,1])) {
                    $this->initPointsAndAnalyzeResultToMatch($line);
                }
            }
        }
        fclose($file);
    }

    public function filterLineAndInitMatch($lineStr) {
        //filter out match name by replacing 'match',':',case-insensitive
        $matchName = $this->filterMatchNameFromLine($lineStr);
        $this->createMatchAndAddToTournament($matchName);

    }

    private function filterMatchNameFromLine($lineStr) {
        $matchName = str_ireplace(['match',':'],['',''],$lineStr);
        $matchName = trim($matchName);
        return $matchName;
    }

    private function createMatchAndAddToTournament($matchName) {
        $tournament = $this->getTournament();
        //if match name not exist, create new one and store in Tournament matches collection
        if(is_null($tournament->getMatchByName($matchName))) {
            if($matchName == '') {
                echo "Invalid Line Number: ".$this->lineCount. ",please check or try new file \n";
                exit;
            }
            //init Match and push to matches collection
            $match = new Match($matchName);
            $tournament->addMatch($match);
        }
    }

    public function filterLineAndInitPlayers($lineStr) {
        $playerNames = $this->filterPlayerNameFromLine($lineStr);
        $this->createPlayersAndAddToTournament($playerNames);
    }

    private function filterPlayerNameFromLine($lineStr) {
        //filter out player names case-insensitive
        $playerNames = str_ireplace(['person','vs'],['',''],$lineStr);
        //transfer the player name string to array
        $playerNames = preg_split('/\s+/', trim($playerNames));
        if(count($playerNames) !== 2) {
            echo "Invalid Line Number: ".$this->lineCount. "I,please check or try new file \n";
            exit;
        }
        return $playerNames;
    }

    private function createPlayersAndAddToTournament($playerNames) {
        $tournament = $this->getTournament();
        $match = $tournament->getLastMatch();
        //query existed Player in tournament, if not exist, add new one
        $playerA = $tournament->getPlayerByName($playerNames[0]);
        if(is_null($playerA)) {
            $playerA = new Player($playerNames[0]);
            $tournament->addPlayer($playerA);
        }
        $playerB =$tournament->getPlayerByName($playerNames[1]);
        if(is_null($playerB)) {
            $playerB = new Player($playerNames[1]);
            $tournament->addPlayer($playerB);
        }
        $match->setPlayerA($playerA);
        $match->setPlayerB($playerB);
    }

    public function initPointsAndAnalyzeResultToMatch($line) {
        $tournament = $this->getTournament();
        $match = $tournament->getLastMatch();
        if(!empty($match)) {
            $point = trim($line);
            $match->addPoints($point);
            $this->analyzePointAndStoreResultToMatch($match,$point);
        }
    }


    public function analyzePointAndStoreResultToMatch($match,$point) {
        $lastScore = $match->getLastScore();
        $score = $this->analyzeScoreByPoint($lastScore,$point);
        $match->addScore($score);
        //game point
        if($score['result'] == 'game') {
            $winner = $this->analyzeGameAndStatPlayers($match,$point);
            if($winner->getSets() == self::SET_WIN_FOR_MATCH) {
                $this->storeMatchResult($match,$point);
            }
        }
    }

    /**
     * calculate score by point, get score according to tennis rules:
     * 1.winner add 1 points, if under 4, result is score
     * 2.when winner >= 4 if ahead 2 point, result is game
     * 3.when winner >= 4 if ahead 1 point, result is advantage
     * 4.when winner >= 4 if ahead 0 point, result is deuce
     */
    public function analyzeScoreByPoint($lastScore, $point) {
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

        if($score[$winner] < 4)
            $score['result'] = self::POINT_TO_SCORE[$score['scoreA']]. ' - ' . self::POINT_TO_SCORE[$score['scoreB']]; //if winner point under 4, result is score
        else {
            $aheadPoint = $score[$winner] - $score[$loser]; // winner ahead points
            if($aheadPoint >= 2) $score['result'] = 'game'; //ahead 2 points, game
            elseif ($aheadPoint == 1) $score['result'] = 'advantage'; //ahead 1 points, advantage
            elseif($aheadPoint == 0) $score['result'] = 'deuce'; //ahead 0 points, deuce
        }
        return $score;
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
    public function analyzeGameAndStatPlayers($match,$point) {
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
     * when match is over,
     * 1. store match result data
     * 2. store match report
     * 3. reset sets to 0 for players
     */
    public function storeMatchResult($match,$point) {
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

    public function getWinnerByPoint($match,$point) {
        if($point == 0) {
            $winner = $match->getPlayerA();
        } else {
            $winner = $match->getPlayerB();
        }
        return $winner;
    }

    public function getLoserByPoint($match, $point) {
        if($point == 0) {
            $loser = $match->getPlayerB();
        } else {
            $loser = $match->getPlayerA();
        }
        return $loser;
    }

    public function displayTournamentData() {
        $tournament = $this->getTournament();
        $matches = $tournament->getMatches();
        $players = $tournament->getPlayers();

        if(count($matches) == 0 && count($players) == 0) {
            echo "can not analyze this file, please use a valid file. \n";
            exit;
        }

        echo "Tournament Result, Matches Names: \n";
        foreach ($matches as $match) {
            echo $match->getName()."\n";
        }

        echo "Tournament Result, Players Names: \n";
        foreach ($players as $player) {
            echo $player->getName()."\n";
        }
    }

    public function inputCommandAndOutputResult() {
        $tooltips = "To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0\n";
        $readline = readline($tooltips);
        while ($readline != 0) {
            if($readline == 1) {
                $this->queryMatchResult();
            }
            if($readline == 2) {
                $this->queryPlayerScore();
            }
            $readline = readline($tooltips);
        }
    }

    private function queryMatchResult() {
        $tournament = $this->getTournament();
        $matchName = readline('To query the match result, please enter the above Match Name (ie:01): ');
        $match = $tournament->getMatchByName($matchName);
        while (empty($match)) {
            $matchName = readline('Match Name not exist, please enter  the above  Match Name (ie:01): ');
            $match = $tournament->getMatchByName($matchName);
        }
        $results = $match->getResults();
        echo $results['report1'] . "\n";
        echo $results['report2'] . "\n";
    }

    private function queryPlayerScore() {
        $tournament = $this->getTournament();
        $playerName = readline('To query the Games Player Person, please enter the above Player Name (ie:A): ');
        $player = $tournament->getPlayerByName($playerName);
        while (empty($player)) {
            $playerName = readline('Player Name not exist, please enter the above Player Name (ie:A): ');
            $player = $tournament->getPlayerByName($playerName);
        }
        $result = $player->getReportInTournament();
        echo $result ."\n";
    }
}
