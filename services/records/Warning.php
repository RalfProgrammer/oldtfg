<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 19/05/14
 * Time: 22:27
 */

require_once($CONFIG->dir . 'services/records/Warning_read.php');

class Warning {

    var $id;
    var $creator;
    var $date;
    var $text;
    var $patient;
    var $scope;
    var $deleted = 0;
    var $other;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(WARNING_TABLE, 'id', $id, 'deleted', 0)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setCreator   ($record->creator);
        $this->setDate      ($record->date);
        $this->setText      (rawurldecode($record->text));
        $this->setPatient   ($record->patient);
        $this->setScope     ($record->scope);
        $this->setDeleted   ($record->deleted);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        $this->other = new StdClass();

        $this->other->read = false;
//
//        $this->start = explode(' ', $this->start);
//        $this->start = $this->start[0];
//        $this->end   = explode(' ', $this->end);
//        $this->end   = $this->end[0];
//
//        $now = strtotime('now');
//        if(strtotime($this->start) > $now){
//            $this->other->status = 'Futuro';
//        }else if(strtotime($this->end) > $now){
//            $this->other->status = 'Presente';
//        }else{
//            $this->other->status = 'Pasado';
//        }
    }

    public static function getPatientWarnings($user_id){
        global $USER;

        $sql = 'SELECT * FROM ' . WARNING_TABLE . " WHERE patient = :p1 AND deleted = 0 AND (scope like '%$USER->rol%' OR creator = :p2) ORDER BY date DESC";
        $warnings = array();
        if($records = db_query($sql, array(':p1' => $user_id, ':p2' => $USER->id))){
            $ids = array();
            foreach($records as $record){
                $Warning = new Warning();
                $Warning->loadFromRecord($record);
                $warnings[$Warning->getId()] = $Warning;
                array_push($ids, $Warning->getId());
            }
            if($read = Warning_read::getRead($ids)){
                foreach($read as $r){
                    $warnings[$r->getWarning()]->other->read = $r->getDate();
                }
            }
        }
        return $warnings;
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getCreator(){return $this->creator;}
    public function setCreator($value){$this->creator = $value;}

    public function getDate(){return $this->date;}
    public function setDate($value){$this->date = $value;}

    public function getText(){return $this->text;}
    public function setText($value){$this->text = $value;}

    public function getPatient(){return $this->patient;}
    public function setPatient($value){$this->patient = $value;}

    public function getScope(){return $this->scope;}
    public function setScope($value){$this->scope = $value;}

    public function getDeleted(){return $this->deleted;}
    public function setDeleted($value){$this->deleted = $value;}


    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function save(){
        $this->text = rawurlencode($this->text);

        if($this->getId()){
            if(!db_update(WARNING_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(WARNING_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->text = rawurldecode($this->text);

        return $this->getId();
    }
    public function saveRead(){
        global $USER;
        if(!db_get(WARNING_READ_TABLE, 'warning', $this->getId(), 'user', $USER->id)){
            $Warning_read = new Warning_read();
            $Warning_read->setUser($USER->id);
            $Warning_read->setWarning($this->getId());
            return ($Warning_read->save()) ? true : false;
        }
        return true;
    }

    public function delete(){
        $this->text    = rawurlencode($this->text);
        $this->deleted = 1;

        if($this->getId()){
            if(!db_update(WARNING_TABLE, $this)){
                return false;
            }
        }else{
            return false;
        }

        $this->text = rawurldecode($this->text);

        return $this->getId();
    }

} 