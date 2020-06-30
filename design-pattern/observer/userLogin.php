<?php

class User implements SplSubject {
    public $loginCount;
    public $hobby;
    public $observers = null;

    public function __construct()
    {
        $this->loginCount = rand(1,20);
        $hobbies = ['sports','music','movies','reading'];
        $randKey = array_rand($hobbies,1);
        $this->hobby = $hobbies[$randKey];
        $this->observers = new SplObjectStorage();
    }

    public function login() {
        // ....login and store to session
        $this->notify();
    }


    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer)
    {
       $this->observers->detach($observer);
    }

    public function notify()
    {
       $this->observers->rewind();
       while ($this->observers->valid()) {
           $observer = $this->observers->current();
           $observer->update($this);
           $this->observers->next();
       }
    }
}

class Security implements SplObserver {

    public function update(SplSubject $user)
    {
       if($user->loginCount < 10) {
           echo "You have ".$user->loginCount." login today\n";
       } else {
           echo "Too many login \n";
       }
    }
}

class Advertise implements SplObserver {

    public function update(SplSubject $user)
    {
        if($user->hobby == 'sports') {
            echo "welcome to NBA news\n";
        } elseif($user->hobby == 'music') {
            echo "welcome to Micheal Jason Concert\n";
        } elseif($user->hobby == 'movies') {
            echo "welcome to the latest movies section\n";
        } elseif($user->hobby == 'reading') {
            echo "welcome to the Amazon online bookstore\n";
        }
    }
}

$user = new User();
$user->attach(new Security());
$user->attach(new Advertise());
$user->login();
