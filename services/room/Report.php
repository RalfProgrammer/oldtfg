<?php

class Report {
    var $id;
    var $event;
    var $called  = false;
    var $start   = false;
    var $end     = false;
    var $report  = '';
    var $absence = 0;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(REPORT_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setEvent     ($record->event);
        $this->setCalled    ($record->called);
        $this->setStart     ($record->start);
        $this->setEnd       ($record->end);
        $this->setReport    (rawurldecode($record->report));
        $this->setAbsence   ($record->absence);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        if($this->getCalled() == '0000-00-00 00:00:00.000000' || $this->getCalled() == '0000-00-00 00:00:00'){
            $this->setCalled(false);
        }else{
            $this->called   = date('Y-m-d H:i:s', strtotime($this->getCalled()));
        }

        if($this->getStart() == '0000-00-00 00:00:00.000000' || $this->getStart() == '0000-00-00 00:00:00'){
            $this->setStart(false);
        }else{
            $this->start    = date('Y-m-d H:i:s', strtotime($this->getStart()));
        }

        if($this->getEnd() == '0000-00-00 00:00:00.000000' || $this->getEnd() == '0000-00-00 00:00:00'){
            $this->setEnd(false);
        }else{
            $this->end      = date('Y-m-d H:i:s', strtotime($this->getEnd()));
        }
    }

    public static function byEvent($event_id){
        $Report = new Report();
        if($record = db_get(REPORT_TABLE, 'event', $event_id)){
            $Report->loadFromRecord($record);
            return $Report;
        }
        return $Report;
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getEvent(){return $this->event;}
    public function setEvent($value){$this->event = $value;}

    public function getCalled(){return $this->called;}
    public function setCalled($value){$this->called = $value;}

    public function getStart(){return $this->start;}
    public function setStart($value){$this->start = $value;}

    public function getEnd(){return $this->end;}
    public function setEnd($value){$this->end = $value;}

    public function getReport(){return $this->report;}
    public function setReport($value){$this->report = $value;}

    public function getAbsence(){return $this->absence;}
    public function setAbsence($value){$this->absence = $value;}

    /*
    ###########################################################################################
                                    PROCESS
    ###########################################################################################
    */

    public function save(){
        $this->report = rawurlencode($this->report);

        if($this->getId()){
            if(!db_update(REPORT_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(REPORT_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }

        $this->report = rawurldecode($this->report);

        return $this->getId();
    }

    public function delete(){

    }

    public function isFinished(){
        if(!$this->end)
            return false;
        $now = strtotime('now');
        $end = strtotime($this->end);
        return $now > $end;
    }

    /*
    ###########################################################################################
                                    DB ACCESS
    ###########################################################################################
    */

    public static function getCompleteReport($user_id){
        $sql = 'SELECT * FROM ' . EVENT_TABLE . ' ev JOIN ' . REPORT_TABLE . ' rp ON ev.user = :p1 AND ev.id = rp.event';
        if($reports = db_query($sql, array(':p1' => $user_id))){
            $text = '';
            foreach($reports as $data){
                $Report = new Report();
                $Report->loadFromRecord($data);
                if($Report->getEnd()){
                    $Event = new Event();
                    $Event->loadFromRecord($data);
                    $Doctor = new Staff($Event->getDoctor());
                    $date = explode(' ', $Report->getStart());
                    $text .=
                        '<div class="historic_report_text">
                            <div class="report_header">
                                <i class="fa fa-angle-right"></i><b>' . $date[0] . '</b> ' . $Event->getRequest() . '
                            </div>
                        <div class="report_doctor">
                            <span>Datos:</span>
                            <div>
                                Doctor: ' . $Doctor->getFullName(). '
                            </div>
                        </div>
                        <div class="report_body">
                            <span>Informe:</span>
                            <div>' .
                        $Report->getReport() . '
                            </div>
                        </div>
                    </div>';
                }
            }
            return $text;
        }
        return '<span class="empty_list">El paciente no tiene ningun informe</span>';
    }
}