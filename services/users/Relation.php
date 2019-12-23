<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 23/03/14
 * Time: 20:12
 */

class Relation {
    var $id;
    var $doctor;
    var $patient;
    var $timestamp;
    var $deleted;

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setDoctor    ($record->doctor);
        $this->setPatient   ($record->patient);
        $this->setTimestamp ($record->timestamp);
        $this->setDeleted   ($record->deleted);
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    public function getDoctor(){return $this->doctor;}
    public function setDoctor($doctor){$this->doctor = $doctor;}

    public function getPatient(){return $this->patient;}
    public function setPatient($patient){$this->patient = $patient;}

    public function getTimestamp(){return $this->timestamp;}
    public function setTimestamp($value){$this->timestamp = $value;}

    public function getDeleted(){return $this->deleted;}
    public function setDeleted($value){$this->deleted = $value;}

    /*
    ###########################################################################################
                                    PROCCESS
    ###########################################################################################
    */

    public static function getRelation($doctor, $patient){
        $rel = false;
        if($record = db_get(RELATION_TABLE, 'doctor', $doctor, 'patient', $patient, 'deleted', '0')){
            $rel = new Relation();
            $rel->loadFromRecord($record);
        }
        return $rel;
    }

    public static function getDoctors($user_id = false, $only_ids = false){
        global $USER;
        $user_id = ($user_id) ? $user_id : $USER->id;

        $doctors = array();
        if($records = db_gets(RELATION_TABLE, 'patient', $user_id, 'deleted', '0')){
            if($only_ids){
                foreach($records as $doctor){
                    $doctors[] = $doctor->doctor;
                }
            }else{
                foreach($records as $doctor){
                    $Staff = new Staff($doctor->doctor);
                    $doctors[] = $Staff;
                }

                function order_staff($a, $b){
                    if($a->getLastname() == $b->getLastname()){
                        return 0;
                    }
                    return ($a->getLastname() < $b->getLastname()) ? -1 : 1;
                }

                usort($doctors, 'order_staff');
            }
        }
        return $doctors;
    }

    public static function getPatients($doctor = false, $only_ids = false){
        global $USER;
        if(!$doctor)
            $doctor = $USER->id;

        $patients = array();
        if($records = db_gets(RELATION_TABLE, 'doctor', $doctor, 'deleted', '0')){
            if($only_ids){
                foreach($records as $doctor){
                    $patients[] = $doctor->doctor;
                }
            }else{
                foreach($records as $patient){
                    $User = new Patient($patient->patient);
                    $patients[] = $User;
                }

                function order_patient($a, $b){
                    if($a->getLastname() == $b->getLastname()){
                        return 0;
                    }
                    return ($a->getLastname() < $b->getLastname()) ? -1 : 1;
                }

                usort($patients, 'order_patient');
            }
        }
        return $patients;
    }

    public static function deleteUserRelations($user_id){
        $sql = 'UPDATE `' . RELATION_TABLE . '` SET deleted = 1 WHERE doctor = :p1 || patient = :p2';
         return db_query($sql, array(':p1' => $user_id, ':p2' => $user_id), 'update');
    }

    /*
    ###########################################################################################
                                    DB
    ###########################################################################################
    */

    public function save(){
        if($this->getId()){
            if(!db_update(RELATION_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(RELATION_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }
        return $this->getId();
    }

    public function delete(){
        if($this->getId()){
            $this->setDeleted(1);
            if(db_update(RELATION_TABLE, $this)){
                return true;
            }
        }
        return false;
    }
} 