<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 6/04/14
 * Time: 13:09
 */

class Event {
    var $id;
    var $user;
    var $doctor;
    var $start;
    var $end;
    var $timestamp;
    var $online;
    var $location;
    var $request;
    var $finished = 0;
    var $other;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(EVENT_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
                return $this;
            }
        }
        return false;
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setUser      (rawurldecode($record->user));
        $this->setDoctor    (rawurldecode($record->doctor));
        $this->setStart     ($record->start);
        $this->setEnd       ($record->end);
        $this->setTimestamp ($record->timestamp);
        $this->setOnline    ($record->online);
        $this->setLocation  ($record->location);
        $this->setRequest   ((($record->request != '')? rawurldecode($record->request) : 'Sin titulo'));
        $this->setFinished  ($record->finished);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        $this->other = new StdClass();

        $date = explode(' ', $this->getStart());
        $this->other->day_start = $date[0];
        $date = explode('-', $date[0]);
        $this->other->day   = $date[2];
        $this->other->year   = $date[0];


        switch($date[1]){
            case 1 : $this->other->month = 'ENE';break;
            case 2 : $this->other->month = 'FEB';break;
            case 3 : $this->other->month = 'MAR';break;
            case 4 : $this->other->month = 'ABR';break;
            case 5 : $this->other->month = 'MAY';break;
            case 6 : $this->other->month = 'JUN';break;
            case 7 : $this->other->month = 'JUL';break;
            case 8 : $this->other->month = 'AGO';break;
            case 9 : $this->other->month = 'SEP';break;
            case 10: $this->other->month = 'OCT';break;
            case 11: $this->other->month = 'NOV';break;
            case 12: $this->other->month = 'DIC';break;
        }

        $date = explode(' ', $this->getEnd());
        $this->other->day_end = $date[0];

        $hour = explode(' ', $this->getStart());
        $hour = explode(':', $hour[1]);
        $this->other->hour = $hour[0] . ':' . $hour[1];

        $hour = explode(' ', $this->getEnd());
        $hour = explode(':', $hour[1]);
        $this->other->hour_end = $hour[0] . ':' . $hour[1];

        $Doctor = new User($this->doctor);
        $this->other->doctor = $Doctor->getFullName();

        $Patient = new User($this->user);
        $this->other->patient = $Patient->getFullName();

        $now = strtotime('now');
        $ev  = strtotime($this->start);

        $this->other->start_time = $ev;
        $this->other->future = ($ev > $now);
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

    public function getDoctor(){return $this->doctor;}
    public function setDoctor($value){$this->doctor = $value;}

    public function getStart(){return $this->start;}
    public function setStart($value){$this->start = $value;}

    public function getEnd(){return $this->end;}
    public function setEnd($value){$this->end = $value;}

    public function getTimestamp(){return $this->timestamp;}
    public function setTimestamp($value){$this->timestamp = $value;}

    public function getOnline(){return $this->online;}
    public function setOnline($value){$this->online = $value;}

    public function getLocation(){return $this->location;}
    public function setLocation($value){$this->location = $value;}

    public function getRequest(){return $this->request;}
    public function setRequest($value){$this->request = $value;}

    public function getFinished(){return $this->finished;}
    public function setFinished($value){$this->finished = $value;}

    /*
    ###########################################################################################
                                    PROCESS
    ###########################################################################################
    */

    public function save(){
        $this->request = rawurlencode($this->request);

        if($this->getId()){
            if(!db_update(EVENT_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(EVENT_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->request = rawurldecode($this->request);

        return $this->getId();
    }

    public function delete(){
        if($this->getId()){
            return db_delete(EVENT_TABLE, $this->getId());
        }else{
            return false;
        }
    }

    /*
    ###########################################################################################
                                    PROCESS
    ###########################################################################################
    */

    public function getDateCalendar(){
        $date = explode(" ", $this->start);
        $date = $date[0];
        $date = explode("-", $date);
        $month = ($date[1] < 10) ? substr($date[1], 1, 1) : $date[1];
        $day   = ($date[2] < 10) ? substr($date[2], 1, 1) : $date[2];
        return $date[0] . $month . $day;
    }

    public function getTimeCalendar(){
        $start = explode(" ", $this->start);
        $start = $start[1];
        $start = explode(":", $start);
        $hour = ($start[0] < 10) ? substr($start[0], 1, 1) : $start[0];
        $Obj = new stdClass();
        $Obj->start = $hour . '.' . $start[1];

        $end = explode(" ", $this->end);
        $end = $end[1];
        $end = explode(":", $end);
        $hour = ($end[0] < 10) ? substr($end[0], 1, 1) : $end[0];
        $Obj->end   = $hour . '.' . $end[1];
        return $Obj;
    }

    public function getFormatDate(){
        $date   = explode(" ", $this->start);
        $start  = explode("-", $date[0]);
        $time   = explode(":", $date[1]);
        return "$start[2]/$start[1]/$start[0] $time[0]:$time[1]";

    }

    public static function getUserEvents($user_id = false, $from = false, $to = false, $only_online = false, $no_finished = false){
        global $USER;
        $user_id = ($user_id) ? $user_id : $USER->id;

        $events = array();
        $params = array(':p1' => $user_id);

        $User = new User($user_id);

        if($User->getRol() == ROL_USER){
            $sql = 'SELECT * FROM ' . EVENT_TABLE . ' WHERE user = :p1';
        }else{
            $sql = 'SELECT * FROM ' . EVENT_TABLE . ' WHERE doctor = :p1';
        }

        if($only_online){
            $sql .= ' AND online = 1';
        }

        if($no_finished){
            $sql .= ' AND finished = 0';
        }

        if($from && $to){
            $sql .= ' AND (UNIX_TIMESTAMP(`start`) >= UNIX_TIMESTAMP(:p2) AND UNIX_TIMESTAMP(`start`) < UNIX_TIMESTAMP(:p3))';
            $params[':p2'] = $from;
            $params[':p3'] = $to;

        }else{
            if($from){
                $params[':p2'] = $from;
                $sql .= ' AND UNIX_TIMESTAMP(`start`) >= UNIX_TIMESTAMP(:p2)';
            }
            if($to){
                $params[':p3'] = $to;
                $sql .= ' AND UNIX_TIMESTAMP(`end`) <= UNIX_TIMESTAMP(:p3)';
            }
        }

        $sql .= ' ORDER BY start ASC';
        if($records = db_query($sql, $params)){
            foreach($records as $record){
                $Event = new Event();
                $Event->loadFromRecord($record);
                $events[strtotime($Event->getStart())] = $Event;
            }
        }

        return $events;
    }

    public static function getUserNext($user_id = false){
        global $USER;
        $user_id = ($user_id) ? $user_id : $USER->id;

        $appointments = array();

        $sql = 'SELECT * FROM ' . EVENT_TABLE . ' WHERE user = :p1 AND start > NOW()';

        if($records = db_query($sql, array(":p1" => $user_id))){
            $Ap = new Event();
            $Ap->loadFromRecord(array_shift($records));
            return $Ap;
        }
        return false;
    }

    public static function testDate($date, $patient = false, $doctor = false){

        if($patient){
            if($records = db_gets(EVENT_TABLE, 'user' , $patient, 'start', $date)){
                return false;
            }
        }
        if($doctor){
            if($records = db_gets(EVENT_TABLE, 'doctor' , $doctor, 'start', $date)){
                return false;
            }
        }

        return true;
    }
}