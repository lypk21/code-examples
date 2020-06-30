<?php

/**
 * in some situations that we need to create many subclasses, like sending msg has different types, like Site, Email, SMS, Slack
 * and each msg has different priority,like common,important,danger. Then we need m * n subclasses. With bridge design pattern, we
 * only need m + n classes. Bridge can prevent the proliferation of classed in the cost of adding a certain extent of coupling.
 */
abstract class Msg {
    protected $sender;

    public function __construct($sender)
    {
        $this->sender = $sender;
    }

    abstract public function addMsg($content);

    public function send($to, $content) {
        $content = $this->addMsg($content);
        $this->sender->send($to, $content);
    }
}

class SiteMsg {
    public function send($to, $content) {
        echo $content.' Site Msg send to '.$to."\n";
    }
}

class EmailMsg {
    public function send($to, $content) {
        echo $content.' Mail send to '.$to."\n";
    }
}

class SmsMsg {
    public function send($to, $content) {
        echo $content.'SMS send to '.$to."\n";
    }
}

class CommonMsg extends Msg {

    public function addMsg($content)
    {
        return 'common: '.$content."\n";
    }
}

class WarningMsg extends Msg {
    public function addMsg($content)
    {
        return 'warning: '.$content."\n";
    }
}

class DangerMsg extends Msg {
    public function addMsg($content)
    {
        return 'danger: '.$content."\n";
    }
}

$commonMsg = new CommonMsg(new SiteMsg());
echo $commonMsg->send('Jack Chen','How are you?');

$wainingMsg = new WarningMsg(new SmsMsg());
echo $wainingMsg->send('Micheal','watch your dog');

$dangerMsg = new DangerMsg(new EmailMsg());
echo $dangerMsg->send('Louis Liu','focus on driving');
