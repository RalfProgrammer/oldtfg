<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 23/03/14
 * Time: 22:50
 */

class Message {
    var $id;
    var $from;
    var $to;
    var $message;
    var $sent;
    var $read = 0;
    var $other;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(MESSAGE_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setFrom      ($record->from);
        $this->setTo        ($record->to);
        $this->setMessage   (nl2br(rawurldecode($record->message)));
        $this->setSent      ($record->sent);
        $this->setRead      ($record->read);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        global $USER;
        $this->read = ($USER->id == $this->from) ? 1 : $this->read;

        $this->other = new StdClass();
        $this->other->time = false;

        $time = (strtotime('now') - strtotime($this->sent)) / 60;
        $date = explode(' ', $this->sent);

        switch(true){
            case $time < 1 :
                $this->other->time      = round($time * 60) . 's';
                $date = explode(':', $date[1]);
                $this->other->time_text = $date[0] . ':' . $date[1];
                break;
            case $time < 60 :
                $this->other->time = round($time) . 'm';
                $date = explode(':', $date[1]);
                $this->other->time_text = $date[0] . ':' . $date[1];
                break;
            case $time < 1440 :
                $this->other->time = round(($time / 60)) .'h';
                $date = explode(':', $date[1]);
                $this->other->time_text = $date[0] . ':' . $date[1];
                break;
            default :
                $this->other->time = round((($time / 60) / 24)) .'d';
                $date = explode('-', $date[0]);
                $this->other->time_text = $date[2] . '/' . $date[1];
        }
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    public function getFrom(){return $this->from;}
    public function setFrom($from){$this->from = $from;}

    public function getTo(){return $this->to;}
    public function setTo($to){$this->to = $to;}

    public function getMessage(){return $this->message;}
    public function setMessage($message){$this->message = $message;}

    public function getSent(){return $this->sent;}
    public function setSent($sent){$this->sent = $sent;}

    public function isRead(){return $this->read;}
    public function setRead($read){$this->read = $read;}

    /*
    ###########################################################################################
                                    FUNCTIONS
    ###########################################################################################
    */

    public function save(){
        $this->message = rawurlencode($this->message);

        if($this->getId()){
            if(!db_update(MESSAGE_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(MESSAGE_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->message = rawurldecode($this->message);
        return $this->getId();
    }

    /*
    ###########################################################################################
                                    FUNCTIONS
    ###########################################################################################
    */

    public static function getConversation($user_id){
        global $USER;
        $messages = array();
        $sql = 'SELECT * FROM ' . MESSAGE_TABLE . ' WHERE (`from` = :p1 AND `to` = :p2) OR (`from` = :p3 AND `to` = :p4) ORDER BY sent ASC';
        if($records = db_query($sql, array( ":p1" => $USER->id, ":p2" => $user_id, ":p3" => $user_id, ":p4" => $USER->id))){
            foreach($records as $record){
                $Message = new Message();
                $Message->loadFromRecord($record);
                $messages[] = $Message;
            }
        }
        return $messages;

    }

    public static function getNoRead($user_id = false){
        global $USER;
        if(!$user_id)
            $user_id = $USER->id;

        $messages = new StdClass();
        $messages->list  = new StdClass();
        $messages->total = 0;

        $sql = 'SELECT * FROM '. MESSAGE_TABLE . ' WHERE `to` = :p1 AND `read` = 0 ORDER BY `id` DESC';
        if($records = db_query($sql, array( ":p1" => $user_id))){
            foreach($records as $record){
                $Message = new Message();
                $Message->loadFromRecord($record);
                if(!isset($messages->list->{$Message->getFrom()}))
                    $messages->list->{$Message->getFrom()} = array();

                array_push($messages->list->{$Message->getFrom()}, $Message);
                $messages->total++;
            }
        }
        return $messages;
    }
} 