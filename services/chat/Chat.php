<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 21/04/14
 * Time: 20:55
 */

require_once('Message.php');

class Chat {
    var $id;
    var $user_A;
    var $user_B;
    var $start;
    var $last;
    var $other;//no DB

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(CHAT_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
                return $this;
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setUser_A    ($record->user_A);
        $this->setUser_B    ($record->user_B);
        $this->setStart     ($record->start);
        $this->setLast      ($record->last);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        global $USER;
        $this->other = new StdClass();
        $User = new User($this->getUser_B());
        $this->other->fullname = $User->getFullName();
        $this->other->avatar   = $User->getSrcAvatar();

        if($messages = Message::getConversation($this->getUser_B())){
            if($msgs_no_read = array_filter($messages, create_function('$u','return $u->read == 0 && $u->from != ' . $USER->id . ';'))){
                $this->other->num_msg  = count($msgs_no_read);
                $first_no_read = array_shift($msgs_no_read);
                $this->other->last_msg = $first_no_read->message;
            }else{
                $this->other->num_msg  = 0;
                $last_msg = array_pop($messages);
                $this->other->last_msg = $last_msg->message;
            }
            if(!isset($last_msg))
                $last_msg = array_pop($messages);

            $this->other->time     = $last_msg->other->time;
        }
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    public function getUser_A(){return $this->user_A;}
    public function setUser_A($value){$this->user_A = $value;}

    public function getUser_B(){return $this->user_B;}
    public function setUser_B($value){$this->user_B = $value;}

    public function getStart(){return $this->start;}
    public function setStart($value){$this->start = $value;}

    public function getLast(){return $this->last;}
    public function setLast($value){$this->last = $value;}

    public function getOther(){return $this->other;}
    public function setOther($value){$this->other = $value;}

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function save(){
        if($this->getId()){
            $this->last = date('Y-m-d H:i:s');
            if(!db_update(CHAT_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(CHAT_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }
        return true;
    }

    public function delete(){
        if($this->getId()){
            if(db_delete(CHAT_TABLE, $this->getId())){
                return true;
            }
        }
        return false;
    }

    /*
    ###########################################################################################
                                    LOAD DATA
    ###########################################################################################
    */

    public static function getChats(){
        global $USER;
        $chats = array();
        $sql = 'SELECT * FROM ' . CHAT_TABLE .' WHERE user_A = :p1 ORDER BY last DESC';
        if($records = db_query($sql, array(':p1' => $USER->id))){
            foreach($records as $record){
                $Chat = new Chat();
                $Chat->loadFromRecord($record);
                array_push($chats, $Chat);
            }
        }
        return $chats;
    }

    public static function hasChat($to = 0){
        global $USER;
        $sql = 'SELECT count(*) as num FROM ' . CHAT_TABLE .' WHERE (user_A = :p1 AND user_B = :p2) OR (user_B = :p1 AND user_A = :p2) ';
        if($record = db_query($sql, array(':p1' => $USER->id, ':p2' => $to))){
            $record = array_pop($record);
            return $record->num;
        }
        return 0;
    }

    public static function canChatWith($user = 0){
        global $USER;
        if(!Chat::hasChat($user)){
            $all = User::get_all();
            return (isset($all[$user])) ? true : false;
        }

        return true;
    }

    public static function openChats($user_a, $user_b){
        global $USER;
        $Chat = new Chat();
        if($chat_a = db_get(CHAT_TABLE, 'user_A', $user_a, 'user_B', $user_b)){
            $Chat->loadFromRecord($chat_a);
            $Chat->save();
        }else{
            $Chat->setUser_A($user_a);
            $Chat->setUser_B($user_b);
        }
        $Chat->save();

        $Chat = new Chat();
        if($chat_a = db_get(CHAT_TABLE, 'user_A', $user_b, 'user_B', $user_a)){
            $Chat->loadFromRecord($chat_a);
            $Chat->save();
        }else{
            $Chat->setUser_A($user_b);
            $Chat->setUser_B($user_a);
        }
        $Chat->save();

        return true;
    }

    public static function markAsRead($user){
        global $USER;
        $sql = 'UPDATE ' . MESSAGE_TABLE . ' SET `read` = 1 WHERE (`read` = 0 AND `from` = :p1 AND `to` = :p2)';

        db_query($sql, array(':p1' => $user, ':p2' => $USER->id), 'update');
    }

}