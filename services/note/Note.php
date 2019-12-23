<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 1/05/14
 * Time: 20:22
 */

class Note {
    var $id;
    var $text;
    var $user;
    var $event;
    var $visible;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(NOTE_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setText      (rawurldecode($record->text));
        $this->setUser      ($record->user);
        $this->setEvent     ($record->event);
        $this->setVisible   ($record->visible);
    }

    public static function getByEventAndUser($event, $user_id = false){
        global $USER;
        if(!$user_id)
            $user_id = $USER->id;

        if($record = db_get(NOTE_TABLE, 'event', $event, 'user', $user_id)){
            $Note = new Note();
            $Note->loadFromRecord($record);
            return $Note;
        }
        return array();
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getText(){return $this->text;}
    public function setText($value){$this->text = $value;}

    public function getUser(){return $this->user;}
    public function setUser($value){$this->user = $value;}

    public function getEvent(){return $this->event;}
    public function setEvent($value){$this->event = $value;}

    public function getVisible(){return $this->visible;}
    public function setVisible($value){$this->visible = $value;}

    /*
    ###########################################################################################
                                    PROCESS
    ###########################################################################################
    */

    public function save(){
        $this->text = rawurlencode($this->text);

        if($this->getId()){
            if(!db_update(NOTE_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(NOTE_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->text = rawurldecode($this->text);

        return $this->getId();
    }

    public function delete(){
        if($this->getId()){
            return db_delete(NOTE_TABLE, $this->getId());
        }
    }
} 