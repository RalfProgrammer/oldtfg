<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 18/04/14
 * Time: 11:24
 */

class Patient extends User{
    var $historic;
    var $user_id;
    var $status;
    var $height;
    var $weight;

    function __construct($id = false){
        if($id){
            if(parent::__construct($id)){
                if($record = db_get(PATIENT_TABLE, 'user_id', $id)){
                    $this->loadPatientRecord($record);
                    return $this;
                }
            }
        }
        return false;
    }

    public function loadPatientRecord($record){
        $this->setHistoric      ($record->historic);
        $this->setUser_id       ($record->user_id);
        $this->setStatus        ($record->status);
        $this->setHeight        ($record->height);
        $this->setWeight        ($record->weight);

        $this->loadExtraPatientInfo();
    }

    public static function get_by_ids($ids){
        $ids = implode(',', $ids);
        $sql = 'SELECT * FROM ' . PATIENT_TABLE . ' WHERE user_id IN (' . $ids .')';

        return db_query($sql, array(':p1' => $ids));
    }

    public function loadExtraPatientInfo(){
        if(!$this->other)
            $this->other = new StdClass();

        $this->other->identifier = $this->historic;
    }

    public function test_type_id($ref_id, $user_id = false){
        if(!$ref_id)
            return false;

        $record = db_get(PATIENT_TABLE, 'historic', $ref_id);
        if($user_id){
            return ($record) ? $record->user_id == $user_id : true;
        }else{
            return ($record) ? false : true;
        }
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getHistoric(){ return $this->historic; }
    public function setHistoric($value){ $this->historic = $value; }

    public function getUser_id(){ return $this->user_id; }
    public function setUser_id($value){ $this->user_id = $value; }

    public function getStatus(){ return $this->branch; }
    public function setStatus($value){ $this->status = $value; }

    public function getHeight(){ return $this->height; }
    public function setHeight($value){ $this->height = $value; }


    public function getWeight(){ return $this->weight; }
    public function setWeight($value){ $this->weight = $value; }

    public function getFname(){
        return $this->getLastname() . ', ' . $this->getName() . ' ( NH: ' . $this->getHistoric() . ' )';
    }

    /*
    ###########################################################################################
                                            DB
    ###########################################################################################
    */

    public function save(){
        if(parent::save()){
            $this->setUser_id($this->getId());
            $this->status = rawurlencode($this->status);
            if(db_get(PATIENT_TABLE, 'user_id', $this->id)){
                if(!db_update(PATIENT_TABLE, $this, 'user_id')){
                    return false;
                }
            }else{
                if(db_insert(PATIENT_TABLE, $this) === false){
                    return false;
                }
            }
            $this->status = rawurldecode($this->status);
        }else{
            return false;
        }
        return true;
    }

}