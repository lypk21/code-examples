<?php

interface ICommand {
    public function onCommand($name, $args);
}

class CommandChain {
    private $_commands = array();

    public function addCommand($cmd)
    {
        $this->_commands[] = $cmd;
    }

    public function runCommand($name,$args = null) {
        foreach ($this->_commands as $command) {
            if($command->onCommand($name,$args)) {
                return;
            }
        }
    }
}

class UserCommand implements ICommand {
    public function onCommand($name, $args)
    {
        if($name != 'addUser') return false;
        echo "UserCommand handling 'addUser'\n";
        return true;
    }
}

class MailCommand implements ICommand {

    public function onCommand($name, $args)
    {
        if($name != 'mail') return false;
        echo "MailCommand handling 'mail'\n";
        return true;
    }
}

$commands = new CommandChain();

$commands->addCommand(new UserCommand());
$commands->addCommand(new MailCommand());

$commands->runCommand('mail');
$commands->runCommand('addUser');
