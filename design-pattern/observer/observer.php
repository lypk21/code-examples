<?php

//event
function buy($observer) {
    echo "buy successfully\n";
    return true;
}

//Observee register observer, when event dispatch, if observer's condition is satisfied, it will be trigger and update.
class Observee {
    private $observers = array();

    //register observer
    public function register($observer) {
        $this->observers[] = $observer;
    }

    public function trigger() {
        if(count($this->observers) > 0) {
            foreach ($this->observers as $observer) {
                $observer->update();
            }
        }
    }
}

interface Observable {
    public function update();
}

//Observer1
class Email implements Observable {

    public function update()
    {
        echo "Email Notify\n";
    }
}
//Observer2
class SMS implements Observable {

    public function update()
    {
        echo "SMS Notify\n";
    }
}

$observee = new Observee();
$observee->register(new Email());
$observee->register(new SMS());
if(buy($observee)) {
    $observee->trigger();
}


