<?php

define('USER_TABLE'             , 'user');
define('RELATION_TABLE'         , 'relation');
define('MESSAGE_TABLE'          , 'message');
define('CHAT_TABLE'             , 'chat');
define('EVENT_TABLE'            , 'event');
define('REPORT_TABLE'           , 'report');
define('PERMISSION_TABLE'       , 'permission');
define('STAFF_TABLE'            , 'staff');
define('PATIENT_TABLE'          , 'patient');
define('NOTE_TABLE'             , 'note');
define('MEDICINE_TABLE'         , 'medicine');
define('ANALYTIC_TABLE'         , 'analytic');
define('PROTOCOL_TABLE'         , 'protocol');
define('WARNING_TABLE'          , 'warning');
define('WARNING_READ_TABLE'     , 'warning_read');
define('LOG_TABLE'              , 'log');
define('CALL_TABLE'             , 'call');

function  db_query($sql, $data = array(), $type = false){
    return execSQL($sql, $data, $type);
}

function db_get($table, $field1, $value1, $field2 = false, $value2 = false, $field3 = false, $value3 = false){
    $sql    = "SELECT * FROM `$table`WHERE ";
    $params = array();
    if($field1 && $value1 !== false){
        $sql .= "$field1 = :p1";
        $params[':p1'] = $value1;
        if($field2 && $value2 !== false){
            $params[':p2'] = $value2;
            $sql .= " AND $field2 = :p2";
            if($field3 && $value3 !== false){
                $params[':p3'] = $value3;
                $sql .= " AND $field3 = :p3";
            }
        }
    }
    if($data = db_query($sql, $params)){
        return array_shift($data);
    }
    return false;
}

function db_gets($table, $field1 = false, $value1 = false, $field2 = false, $value2 = false, $field3 = false, $value3 = false){
    $sql    = "SELECT * FROM `$table`";
    $params = array();
    if($field1 && $value1 !== false){
        $sql .= ' WHERE ';
        $sql .= "`$field1` = :p1";
        $params[':p1'] = $value1;
        if($field2 && $value2 !== false){
            $params[':p2'] = $value2;
            $sql .= " AND `$field2` = :p2";
            if($field3 && $value3 !== false){
                $params[':p3'] = $value3;
                $sql .= " AND $field3 = :p3";
            }
        }
    }
    if($data = db_query($sql, $params)){
        return $data;
    }
    return false;

}

function db_insert($table, $object){

    $sql    = "INSERT INTO `$table`";
    $params = array();

    $columns = db_get_columns_names($table);

    $keys   = '';
    $values = '';
    $count = 1;
    foreach($object as $key => $value){
        if(isset($value) && $value !== false && in_array($key, $columns)){
            $keys    .= "`$key`,";
            $values  .= " :p$count,";
            $params[":p$count"] = $value;
            $count++;
        }
    }

    $keys   = substr($keys, 0, strlen($keys) - 1);
    $values = substr($values, 0, strlen($values) - 1);

    $sql .= " ($keys) VALUES ($values)";

    return db_query($sql, $params, 'insert');
}

function db_update($table, $object, $key_name = false){
    if(!isset($object->id))
        return false;

    $key_name = ($key_name)? $key_name : 'id';

    $columns = db_get_columns_names($table);

    $sql    = "UPDATE `$table` SET";
    $params = array();

    $count = 1;
    foreach($object as $key => $value){
        if($value !== false && in_array($key, $columns) && $key != $key_name){
            $sql     .= " `$key` = :p$count, ";
            $params[":p$count"] = $value;
            $count++;
        }
    }
    $sql   = substr($sql, 0, strlen($sql) - 2);

    $sql .= ' WHERE ' . $key_name . ' = :p' . $count;
    $params[":p$count"] = $object->{$key_name};

    return db_query($sql, $params, 'update') !== false;
}

function db_delete($table, $id){
    if(!$id)
        return false;

    $sql    = 'DELETE FROM ' . $table .' WHERE `id` = :p1';
    $params = array(":p1" => $id);

    return db_query($sql, $params, 'delete') !== false;
}

function db_get_columns_names($table){
    global $CONFIG;
    $sql     = 'SELECT column_name FROM INFORMATION_SCHEMA.Columns where TABLE_NAME = :p1 AND TABLE_SCHEMA = :p2';
    $columns = db_query($sql, array(':p1' => $table, ':p2' => $CONFIG->db_name));
    $names   = array();
    foreach($columns as $column){
        array_push($names, $column->column_name);
    }
    return $names;
}

//close true on inserts
function execSQL($sql, $params = array(), $type = false){
    global $CONFIG;

    try{
        $db_connection = new PDO('mysql:host=' . $CONFIG->db_dir . ';dbname=' . $CONFIG->db_name, $CONFIG->db_user, $CONFIG->db_pass);
        $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = $db_connection->prepare($sql);

        foreach($params as $i => $param){
            $query->bindValue($i, $param, PDO::PARAM_STR);
        }


        $query->execute();

        switch($type){
            case 'insert':
                return $db_connection->lastInsertId();
                break;
            case 'delete':
            case 'update':
                return $query->rowCount();
                break;
            default:
                return $query->fetchAll(PDO::FETCH_OBJ);
        }

    }catch (PDOException $e){
        debugPHP($e, 'DB ERROR');
        return false;
    }
}

?>