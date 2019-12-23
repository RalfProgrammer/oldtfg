<?php

class Analytic {
    var $id;
    var $type;
    var $user;
    var $creator;
    var $result;
    var $date;
    var $deleted = 0;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(ANALYTIC_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setType      ($record->type);
        $this->setUser      ($record->user);
        $this->setCreator   ($record->creator);
        $this->setResult    (json_decode($record->result));
        $this->setDate      ($record->date);
        $this->setDeleted   ($record->deleted);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        $this->date = strtotime($this->date);
        $this->date = date('Y-m-d H:i', $this->date);
    }

    public static function attributes($type){
        switch($type){
            case 1: return array(1 => 'Linf', 2 => '%T4', 3=> 'T4 Abs', 4 => '%T8', 5=> 'T8 Abs');break;
            case 2: return array(1 => 'Carga', 2 => 'Dif', 3=> 'Log');break;
            default : return array();
        }
    }

    public static function patient($user_id = false){
        global $USER;
        if(!$user_id)
            $user_id = $USER->id;

        $analytics = new StdClass();

        $sql = 'SELECT * FROM ' . ANALYTIC_TABLE . ' WHERE user = :p1 AND deleted = 0 ORDER BY date DESC';
        if($records = db_query($sql, array(':p1' => $user_id))){
            foreach($records as $record){
                $Analytic = new Analytic();
                $Analytic->loadFromRecord($record);
                if(!isset($analytics->{$Analytic->getType()}))
                    $analytics->{$Analytic->getType()} = array();

                array_push($analytics->{$Analytic->getType()}, $Analytic);
            }
        }
        return $analytics;
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getType(){return $this->type;}
    public function setType($value){$this->type = $value;}

    public function getUser(){return $this->user;}
    public function setUser($value){$this->user = $value;}

    public function getCreator(){return $this->creator;}
    public function setCreator($value){$this->creator = $value;}

    public function getResult(){return $this->result;}
    public function setResult($value){$this->result = $value;}

    public function getDate(){return $this->date;}
    public function setDate($value){$this->date = $value;}

    public function getDeleted(){return $this->deleted;}
    public function setDeleted($value){$this->deleted = $value;}


    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function save(){
        if(!is_string($this->result))
            $this->result = json_encode($this->result);

        if($this->getId()){
            if(!db_update(ANALYTIC_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(ANALYTIC_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->result = json_decode($this->result);

        return $this->getId();
    }

    public function delete(){
        if(!is_string($this->result))
            $this->result = json_encode($this->result);
        $this->deleted = 1;

        if($this->getId()){
            if(!db_update(ANALYTIC_TABLE, $this)){
                return false;
            }
        }else{
            return false;
        }

        $this->result = json_decode($this->result);

        return $this->getId();
    }
}