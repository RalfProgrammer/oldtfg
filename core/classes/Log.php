<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 24/05/14
 * Time: 14:41
 */

class Log {
    var $id;
    var $user;
    var $tool;
    var $action;
    var $element;
    var $date;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(USER_TABLE, 'id', $id, 'deleted', 0)){
                $this->loadFromRecord($record);
                return $this;
            }
        }
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setUser      ($record->user);
        $this->setTool      ($record->tool);
        $this->setAction    ($record->action);
        $this->setElement   ($record->action);
        $this->setDate      ($record->date);
    }

    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    public function getUser(){return $this->user;}
    public function setUser($value){$this->user = $value;}

    public function getTool(){return $this->tool;}
    public function setTool($value){$this->tool = $value;}

    public function getAction(){return $this->action;}
    public function setAction($value){$this->action = $value;}

    public function getElement(){return $this->element;}
    public function setElement($value){$this->element = $value;}

    public function getDate(){return $this->date;}
    public function setDate($value){$this->date = $value;}

    public static function create($tool = false, $action = 1, $element = 0){
        if($tool !== false){
            global $USER;

            if(is_string($tool))
                $tool   = Log::toolRelation($tool);

            if(is_string($action))
                $action = Log::actionRelation($action);

            $Log = new Log();
            $Log->setUser       ($USER->id);
            $Log->setTool       ($tool);
            $Log->setAction     ($action);
            $Log->setElement    ($element);

            db_insert(LOG_TABLE, $Log);
        }
    }

    public static function toolRelation($value){
        $values = array(
            'main'          => '0',
            'calendar'      => '1',
            'chat'          => '2',
            'medicine'      => '3',
            'note'          => '4',
            'patients'      => '5',
            'records'       => '6',
            'permissions'   => '7',
            'room'          => '8',
            'staff'         => '9',
            'users'         => '10'
        );
        return $values[$value];
    }

    public static function actionRelation($value){
        $values = array(
            'view'      => '1',
            'edit'      => '2',
            'create'    => '3',
        );
        return $values[$value];
    }
}