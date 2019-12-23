<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 11/05/14
 * Time: 18:24
 */

class Protocol {

    var $id;
    var $user;
    var $creator;
    var $name;
    var $start;
    var $end;
    var $deleted = 0;
    var $other;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(PROTOCOL_TABLE, 'id', $id, 'deleted', 0)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setUser      ($record->user);
        $this->setCreator   ($record->creator);
        $this->setName      (rawurldecode($record->name));
        $this->setStart     ($record->start);
        $this->setEnd       ($record->end);
        $this->setDeleted   ($record->deleted);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        $this->other = new StdClass();

        $this->start = explode(' ', $this->start);
        $this->start = $this->start[0];
        $this->end   = explode(' ', $this->end);
        $this->end   = $this->end[0];

        $now = strtotime('now');
        if(strtotime($this->start) > $now){
            $this->other->status = 'Futuro';
        }else if(strtotime($this->end) > $now){
            $this->other->status = 'Presente';
        }else{
            $this->other->status = 'Pasado';
        }
    }

    public static function getByUser($user_id){
        $sql = 'SELECT * FROM ' . PROTOCOL_TABLE . ' WHERE user = :p1 AND deleted = 0 ORDER BY end DESC';
        $protocols = array();
        if($records = db_query($sql, array(':p1' => $user_id))){
            foreach($records as $record){
                $Protocol = new Protocol();
                $Protocol->loadFromRecord($record);

                array_push($protocols, $Protocol);
            }
        }
        return $protocols;
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getUser(){return $this->user;}
    public function setUser($value){$this->user = $value;}

    public function getCreator(){return $this->creator;}
    public function setCreator($value){$this->creator = $value;}

    public function getName(){return $this->name;}
    public function setName($value){$this->name = $value;}

    public function getStart(){return $this->start;}
    public function setStart($value){$this->start = $value;}

    public function getEnd(){return $this->end;}
    public function setEnd($value){$this->end = $value;}

    public function isDeleted(){return $this->deleted;}
    public function setDeleted($value){$this->deleted = $value;}


    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function save(){
        $this->name = rawurlencode($this->name);

        if($this->getId()){
            if(!db_update(PROTOCOL_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(PROTOCOL_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->name = rawurldecode($this->name);

        return $this->getId();
    }

    public function delete(){
        $this->name    = rawurlencode($this->name);
        $this->deleted = 1;

        if($this->getId()){
            if(!db_update(PROTOCOL_TABLE, $this)){
                return false;
            }
        }else{
            return false;
        }

        $this->name = rawurldecode($this->name);

        return $this->getId();
    }
}