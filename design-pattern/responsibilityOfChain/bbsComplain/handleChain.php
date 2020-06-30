<?php

abstract class Handler {
    protected $superior;

    abstract public function handle($dangerLevel);
}

class Moderators extends Handler {
    const POWER_LEVEL = 1;
    public function handle($dangerLevel)
    {
        if($dangerLevel <= self::POWER_LEVEL) {
            echo 'Delete Post';
        } else {
            $superior = $this->getSuperior();
            $superior->handle($dangerLevel);
        }
    }

    function getSuperior() {
        if(empty($this->superior)) $this->superior = new Administrator();
        return $this->superior;
    }
}

class Administrator extends Handler {
    const POWER_LEVEL = 2;
    public function handle($dangerLevel)
    {
        if($dangerLevel <= self::POWER_LEVEL) {
            echo 'Delete Account';
        } else {
            $superior = $this->getSuperior();
            $superior->handle($dangerLevel);
        }
    }

    function getSuperior() {
        if(empty($this->superior)) $this->superior = new Police();
        return $this->superior;
    }
}

class Police extends Handler {


    public function handle($dangerLevel)
    {
       echo 'Arrest people';
    }
}

$dangerLevel = $_POST['complaint'];
$moderators = new Moderators();
$moderators->handle($dangerLevel);
