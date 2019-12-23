<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 18/04/14
 * Time: 11:24
 */

class Staff extends User{
    var $staff_id;
    var $user_id;
    var $branch;
    var $horary;
    var $office;
    var $room;
    var $h_phone;

    function __construct($id = false){
        if($id){
            if(parent::__construct($id)){
                if($record = db_get(STAFF_TABLE, 'user_id', $id)){
                    $this->loadStaffRecord($record);
                    return $this;
                }
            }
        }
        return false;
    }

    public function loadStaffRecord($record){
        $this->setStaff_id      ($record->staff_id);
        $this->setUser_id       ($record->user_id);
        $this->setBranch        ($record->branch);
        $this->setHorary        ($record->horary);
        $this->setOffice        ($record->office);
        $this->setRoom          ($record->room);
        $this->setH_phone       ($record->h_phone);

        $this->loadExtraStaffInfo();
    }

    public static function get_by_ids($ids){
        $ids = implode(',', $ids);
        $sql = 'SELECT * FROM ' . STAFF_TABLE . ' WHERE user_id IN (' . $ids .')';

        return db_query($sql, array(':p1' => $ids));
    }

    public function loadExtraStaffInfo(){
        if(!$this->other)
            $this->other = new StdClass();
        $this->other->branch_name = Staff::getBranchNames()[$this->branch];
        $this->other->horary_val = new StdClass();
        $this->other->horary_val->morning = (strrpos($this->horary, 'M') !== false) ? true : false;
        $this->other->horary_val->evening = (strrpos($this->horary, 'E') !== false) ? true : false;
        $this->other->horary_val->night   = (strrpos($this->horary, 'N') !== false) ? true : false;

        $this->other->horary_text = "";
        if($this->other->horary_val->morning)
            $this->other->horary_text .= 'Mañana ';

        if($this->other->horary_val->evening)
            $this->other->horary_text .= ' Tarde ';

        if($this->other->horary_val->night)
            $this->other->horary_text .= ' Noche';

        $this->other->identifier = $this->staff_id;
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getStaff_id(){ return $this->staff_id; }
    public function setStaff_id($value){ $this->staff_id = $value; }

    public function getUser_id(){ return $this->user_id; }
    public function setUser_id($value){ $this->user_id = $value; }

    public function getBranch(){ return $this->branch; }
    public function setBranch($value){ $this->branch = $value; }

    public function getHorary(){ return $this->horary; }// M:morning, E: evening, N:night
    public function setHorary($value){ $this->horary = $value; }

    public function getOffice(){ return $this->office; }
    public function setOffice($value){ $this->office = $value; }

    public function getRoom(){ return $this->room; }
    public function setRoom($value){ $this->room = $value; }

    public function getH_phone(){ return $this->h_phone; }
    public function setH_phone($value){ $this->h_phone = $value; }


    public function getFname(){
        return $this->getLastname() . ', ' . $this->getName() . ' ( ID: ' . $this->getStaff_id() . ' )';
    }

    public function test_type_id($ref_id, $user_id = false){
        if(!$ref_id)
            return false;
        $record = db_get(STAFF_TABLE, 'staff_id', $ref_id);
        if($user_id){
            return ($record)? $record->user_id == $user_id : true;
        }else{
            return ($record) ? false : true;
        }
    }

    /*
    ###########################################################################################
                                            DB
    ###########################################################################################
    */

    public static function getBranchNames(){
        return array(
            '1' => 'Alergologia',
            '2' => 'Anestesiologia',
            '3' => 'Angiologia',
            '4' => 'Bariatria',
            '5' => 'Cardiologia',
            '6' => 'Cirugia General',
            '7' => 'Cirugia Maxilofacial',
            '8' => 'Cirugia Plastica',
            '9' => 'Cirugia Estetica',
            '-1' => 'Citaciones (Administrativo)',
            '10' => 'Dermatologia',
            '11' => 'Enocrinologia',
            '12' => 'Endoscopia',
            '13' => 'Fisiatria',
            '14' => 'Gastroenterologia',
            '15' => 'Geriatria',
            '16' => 'Ginecologia',
            '17' => 'Hematologia',
            '18' => 'Homeopatía',
            '19' => 'Infectologia',
            '20' => 'Inmunologia',
            '21' => 'Medicina general',
            '22' => 'Microcirugia',
            '23' => 'Nefrologia',
            '24' => 'Neonatologia',
            '25' => 'Neumologia',
            '26' => 'Neurologia',
            '27' => 'Neurocirugia',
            '28' => 'Nutricion',
            '29' => 'Oftalmologia',
            '30' => 'Oncologia',
            '31' => 'Ortopedia',
            '32' => 'Otorrinolaringologia',
            '33' => 'Pediatria',
            '34' => 'Patologia',
            '35' => 'Perinatologia',
            '36' => 'Proctologia',
            '37' => 'Psiquiatria',
            '38' => 'Radiologia',
            '-2' => 'Recepcionista (Administrativo)',
            '39' => 'Reumatologia',
            '-3' => 'Secretario/a (Administrativo)',
            '40' => 'Traumatologia',
            '41' => 'Urologia',
            '42' => 'Otros (P.Sanitario)',
            '-4' => 'Otros (P.Administrativo)',
        );
    }

    public function generateFreeDates($from, $to = false){
        $events = Event::getUserEvents($this->id, $from);

        $dates = array();
        $m = strpos($this->horary, 'M') !== false;
        $e = strpos($this->horary, 'E') !== false;
        $n = strpos($this->horary, 'N') !== false;

        if($to)
            $to = strtotime($to);

        $now = strtotime('now');

        while(count($dates) < 10 || $to){
            $hour = explode(' ', $from);
            $hour = explode(':', $hour[1]);
            $hour = $hour[0] .':'. $hour[1];

            switch(true){
                case ($hour > '07:59' && $hour < '15:00' && !$m):
                    $from = explode(' ', $from);
                    $from = explode('-', $from[0]);
                    $from = $from[0] . '-' . $from[1] . '-' . $from[2];
                    $from .= ($e)? ' 15:00' : ' 22:00';
                    $from = date('Y-m-d H:i', strtotime($from));
                    break;
                case ($hour > '14:59' && $hour < '22:00' && !$e):
                    $from = explode(' ', $from);
                    $from = explode('-', $from[0]);
                    $from = $from[0] . '-' . $from[1] . '-' . $from[2];
                    if($n){
                        $from .= ' 22:00';
                        $from = date('Y-m-d H:i', strtotime($from));
                    }else{
                        $from .= ' 08:00';
                        $from = date('Y-m-d H:i', strtotime('+24 hours', strtotime($from)));
                    }
                    break;
                case ($hour > '21:59' && $hour < '08:00' && !$n):
                    $from = date('Y-m-d H:i', strtotime('+24 hours', $from));
                    $from = explode(' ', $from);
                    $from = explode('-', $from[0]);
                    $from = $from[0] . '-' . $from[1] . '-' . $from[2];
                    $from .= ($m)? ' 08:00' : ' 15:00';
                    $from = date('Y-m-d H:i', strtotime($from));
                    break;
            }

            $time = strtotime($from);

            if($to && $time > $to)
                break;

            if(!isset($events[$time]) && $now < $time){
                $dates[$time] = $from;
            }
            $from = strtotime ( '+30 minute' , strtotime($from)) ;
            $from = date('Y-m-d H:i' , $from);
        }
        return $dates;
    }

    /*
    ###########################################################################################
                                            DB
    ###########################################################################################
    */

    public function save(){
        if(parent::save()){
            $this->setUser_id($this->getId());
            if(db_get(STAFF_TABLE, 'user_id', $this->user_id)){
                if(!db_update(STAFF_TABLE, $this, 'user_id')){
                    return false;
                }
            }else{
                if(db_insert(STAFF_TABLE, $this) === false){
                    return false;
                }
            }
        }else{
            return false;
        }
        return $this->getStaff_id();
    }

}