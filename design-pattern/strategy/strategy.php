<?php

interface IStrategy {
    function filter($record);
}

class FindAfterStrategy implements IStrategy {
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    function filter($record)
    {
        return strcmp($this->_name,$record) <= 0;
    }
}

class RandomStrategy implements IStrategy {
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    function filter($record)
    {
       return rand(0,1) >= 0.5;
    }
}

class UserList {
    private $_names = array();

    public function __construct($names)
    {
        if(!empty($names) && is_array($names)) {
            foreach ($names as $name) {
                $this->_names[] = $name;
            }
        }
    }

    public function addName($name) {
        if (!empty($name)) $this->_names[] = $name;
    }

    public function find($filter) {
        $recs = array();
        foreach ($this->_names as $name) {
            if($filter->filter($name)) {
                $recs[] = $name;
            }
        }
        return $recs;
    }
}

$users = new UserList(['Andy','Jack','Lori',"Megan"]);
$filterUsers = $users->find(new FindAfterStrategy('J'));
print_r($filterUsers);

$filterUsers = $users->find(new RandomStrategy('K'));
print_r($filterUsers);
