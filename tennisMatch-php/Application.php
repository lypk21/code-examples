<?php
include 'Model/Tournament.php';
include 'Model/Match.php';
include 'Model/Player.php';
include 'MatchProcess.php';

class Application
{
    private $file;
    private $lineCount = 0; //tracing the error for line number
    private $tournament;


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
            $matchProcess = new MatchProcess();
            $matchProcess->analyzePointAndStoreResultToMatch($match,$point);
        }
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
