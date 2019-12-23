<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 19/05/14
 * Time: 22:57
 */

class Warning_read {

    var $id;
    var $warning;
    var $user;
    var $date;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(WARNING_READ_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setWarning   ($record->warning);
        $this->setUser      ($record->user);
        $this->setDate      ($record->date);
    }

    public static function getRead($ids = array()){
        global $USER;
        $sql = 'SELECT * FROM ' . WARNING_READ_TABLE . ' WHERE warning IN (' . join(',', $ids) . ') AND user = :p1';
        $warnings_read = array();
        if($records = db_query($sql, array(':p1' => $USER->id))){
            foreach($records as $record){
                $Warning_read = new Warning_read();
                $Warning_read->loadFromRecord($record);

                array_push($warnings_read, $Warning_read);
            }
        }
        return $warnings_read;
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getWarning(){return $this->warning;}
    public function setWarning($value){$this->warning = $value;}

    public function getUser(){return $this->user;}
    public function setUser($value){$this->user = $value;}

    public function getDate(){return $this->date;}
    public function setDate($value){$this->date = $value;}


    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function save(){
        if($this->getId()){
            if(!db_update(WARNING_READ_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(WARNING_READ_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        return $this->getId();
    }
} 