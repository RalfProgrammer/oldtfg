<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 28/05/14
 * Time: 0:33
 */

class Call {
    var $id;
    var $caller;
    var $receptor;
    var $event;
    var $date  = false;
    var $start = false;
    var $end   = false;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(CALL_TABLE, 'id', $id, 'deleted', 0)){
                $this->loadFromRecord($record);
                return $this;
            }
        }
        return false;
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setCaller    ($record->caller);
        $this->setReceptor  ($record->receptor);
        $this->setEvent     ($record->event);
        $this->setDate      ($record->date);
        $this->setStart     ($record->start);
        $this->setEnd       ($record->end);
    }

    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    public function getCaller(){return $this->caller;}
    public function setCaller($value){$this->caller = $value;}

    public function getReceptor(){return $this->receptor;}
    public function setReceptor($value){$this->receptor = $value;}

    public function getEvent(){return $this->event;}
    public function setEvent($value){$this->event = $value;}

    public function getDate(){return $this->date;}
    public function setDate($value){$this->date = $value;}

    public function getStart(){return $this->start;}
    public function setStart($value){$this->start = $value;}

    public function getEnd(){return $this->end;}
    public function setEnd($value){$this->end = $value;}

    public static function getActualCall(){
        global $USER;
        $now = strtotime('-1 min', strtotime('now'));
        $now = date('Y-m-d H:i:s', $now);
        $sql = 'SELECT * FROM `' . CALL_TABLE . '` WHERE `receptor` = :p1 AND `date` > :p2 ORDER BY id DESC';
        if($records = db_query($sql , array(':p1' => $USER->id, ':p2' => $now))){
            return array_shift($records);
        }
        return false;
    }

    public function save(){
        if($this->getId()){
            if(!db_update(CALL_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(CALL_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }
        return $this->getId();
    }
} 