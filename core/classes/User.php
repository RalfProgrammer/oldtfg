<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 22/03/14
 * Time: 12:10
 */

class User {
    var $id = false;
    var $name;
    var $lastname;
    var $contact;
    var $dni;
    var $birthdate;
    var $password    = '5d772c6e61428f182815b61998f38c7434282a3b';
    var $avatar;
    var $sex;
    var $blood;
    var $information;
    var $deleted     = 0;
    var $sip_id      = 'denispfg2@sip2sip.info';
    var $sip_name    = 'denispfg2';
    var $sip_pass    = '250707s2s';
    var $rol         = 1;
    var $rol_perms   = 0;
    var $other       = false; //no save in db

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(USER_TABLE, 'id', $id, 'deleted', 0)){
                $this->loadFromRecord($record);
                return $this;
            }
        }
        return false;
    }

    public static function get_by_dni($dni){
        $User = false;
        if($record = db_get(USER_TABLE, 'dni', $dni, 'deleted', 0)){
            $User = new User();
            $User->loadFromRecord($record);
        }
        return $User;
    }

    public static function get_all(){
        global $USER;
        $records = false;
        $users = array();

        if(Permission::can_view(PERMISSION_USERS)){
            $records = db_gets(USER_TABLE, "deleted", 0);
        }else{
            switch($USER->rol){
                case ROL_USER://Todos los medicos suyos
                    $sql = 'SELECT user.* FROM ' . USER_TABLE . ' JOIN ' . RELATION_TABLE . ' ON user.id = relation.doctor WHERE relation.patient = :p1 AND user.deleted = 0';
                    $records = db_query($sql, array(':p1' => $USER->id));
                    break;
                case ROL_DOCTOR://Pacientes y todos los administrativos
                    $sql      = 'SELECT user.* FROM ' . USER_TABLE . ' JOIN ' . RELATION_TABLE . ' ON user.id = relation.patient WHERE relation.doctor = :p1 AND user.deleted = 0';
                    $patients = db_query($sql, array(':p1' => $USER->id));
                    $personal = db_gets(USER_TABLE, 'deleted', 0 , 'rol', 2);
                    $records = array_merge($personal, $patients);
                    break;
                case ROL_AUXILIAR:
                case ROL_ADMIN: // Todos los usuarios
                    $records = db_gets(USER_TABLE, "deleted", 0);
                    break;
            }
        }

        if($records){
            $user_types = new StdClass();
            $user_types->staff    = array();
            $user_types->patients = array();

            foreach($records as $record){
                switch($record->rol){
                    case 1:
                        $User = new Patient();
                        $user_types->patients[] = $record->id;
                        break;
                    case 2:
                    case 3:
                        $User = new Staff();
                        $user_types->staff[] = $record->id;
                        break;
                    default:
                        $User = new User();
                }

                $User->loadFromRecord($record);
                unset($User->password);
                $users[$User->getId()] = $User;
            }
            
            if(count($user_types->staff) > 0 && $staff_data = Staff::get_by_ids($user_types->staff)){
                foreach($staff_data as $record){
                    $Staff = $users[$record->user_id];
                    $Staff->loadStaffRecord($record);
                }
            }
            if(count($user_types->patients) > 0 && $patient_data = Patient::get_by_ids($user_types->patients)){
                foreach($patient_data as $record){
                    $Patient = $users[$record->user_id];
                    $Patient->loadPatientRecord($record);
                }
            }
        }
        return $users;
    }

    public function loadFromRecord($record){
        $this->setId            ($record->id);
        $this->setName          (rawurldecode($record->name));
        $this->setLastname      (rawurldecode($record->lastname));
        $this->setContact       (json_decode($record->contact));
        $this->setDni           ($record->dni);
        $this->setBirthdate     ($record->birthdate);
        $this->setPassword      ($record->password);
        $this->setAvatar        ($record->avatar);
        $this->setSex           ($record->sex);
        $this->setBlood         ($record->blood);
        $this->setInformation   (rawurldecode($record->information));
        $this->setDeleted       ($record->deleted);
        $this->setSip_id        ($record->sip_id);
        $this->setSip_name      ($record->sip_name);
        $this->setSip_pass      ($record->sip_pass);
        $this->setRol           ($record->rol);
        $this->setRol_perms     ($record->rol_perms);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        $this->other = new StdClass();
        $this->other->avatar_src = $this->getSrcAvatar();
        $this->other->fullname   = $this->getFullName();

        if(is_string($this->contact))
            $this->contact = json_decode($this->contact);

        $this->other->phones = "";
        if($this->contact->phone && count($this->contact->phone) > 0)
            $this->other->phones = $this->contact->phone[0];

        $this->other->emails = "";
        if($this->contact->email && count($this->contact->email) > 0)
            $this->other->emails = $this->contact->email[0];

        $this->other->identifier = $this->id;

        $now        = strtotime('now');
        $birth_date = strtotime($this->getBirthdate());
        $birth_date = $now - $birth_date;
        $birth_date /= 31556926;
        $birth_date = explode('.', $birth_date);
        $this->other->years = $birth_date[0];
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}

    public function getLastname(){return $this->lastname;}
    public function setLastname($lastname){$this->lastname = $lastname;}

    public function getContact(){return $this->contact;}
    public function setContact($value){$this->contact = $value;}

    public function getDni(){return $this->dni;}
    public function setDni($dni){$this->dni = $dni;}

    public function getBirthdate(){return $this->birthdate;}
    public function setBirthdate($value){$this->birthdate = $value;}

    public function getPassword(){return $this->password;}
    public function setPassword($password){$this->password = $password;}

    public function getAvatar(){return $this->avatar;}
    public function setAvatar($avatar){$this->avatar = $avatar;}

    public function getSex(){return $this->sex;}
    public function setSex($sex){$this->sex = $sex;}

    public function getBlood(){return $this->blood;}
    public function setBlood($value){$this->blood = $value;}

    public function getInformation(){return $this->information;}
    public function setInformation($value){$this->information = $value;}

    public function isDeleted(){return $this->deleted;}
    public function setDeleted($deleted){$this->deleted = $deleted;}

    public function getSip_id(){return $this->sip_id;}
    public function setSip_id($sip_id){$this->sip_id = $sip_id;}

    public function getSip_name(){return $this->sip_name;}
    public function setSip_name($sip_name){$this->sip_name = $sip_name;}

    public function getSip_pass(){return $this->sip_pass;}
    public function setSip_pass($sip_pass){$this->sip_pass = $sip_pass;}

    public function getRol(){return $this->rol;}
    public function setRol($value){$this->rol = $value;}

    public function getRol_perms(){return $this->rol_perms;}
    public function setRol_perms($value){$this->rol_perms = $value;}

    public function getOther(){return $this->other;}
    public function setOther($value){$this->other = $value;}



    public function getFullName(){
        return $this->getName() . ' ' . $this->getLastname();
    }

    public function getFname(){
        return $this->getLastname() . ', ' . $this->getName();
    }

    public function getSrcAvatar(){
        global $CONFIG;
        $src = $CONFIG->www . 'resources/images/avatars/';
        if($this->avatar){
            $src .= $this->getAvatar();
        }else{
            switch($this->rol){
                case ROL_USER: $src .= ($this->sex == "male")? 'user_man.png' : 'user_woman.png';break;
                case ROL_DOCTOR: $src .= 'doctor.png';break;
                case ROL_AUXILIAR: $src .= 'auxiliar.png';break;
                case ROL_ADMIN: $src .= 'admin.png';break;
            }
        }
        return $src;
    }

    public function loadPermissions(){
        $perms_id = ($this->rol_perms) ? $this->rol_perms : $this->rol * -1;
        return Permission::permStructure($perms_id);
    }

    public function test_type_id($ref_id, $user_id){
        return true;
    }

    /*
    ###########################################################################################
                                    DB
    ###########################################################################################
    */

    public function save(){
        $this->name        = rawurlencode($this->name);
        $this->lastname    = rawurlencode($this->lastname);
        $this->information = rawurlencode($this->information);

        if(!is_string($this->contact))
            $this->contact = json_encode($this->contact);

        if($this->getId()){
            if(!db_update(USER_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(USER_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->name        = rawurldecode($this->name);
        $this->lastname    = rawurldecode($this->lastname);
        $this->information = rawurldecode($this->information);
        $this->contact = json_decode($this->contact);

        return $this->getId();
    }

    /*
    ###########################################################################################
                                    PROCCESS
    ###########################################################################################
    */

    public function sendEmail(){
        /* TODO */
        return true;
    }
}