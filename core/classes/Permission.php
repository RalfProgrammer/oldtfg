<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 11/04/14
 * Time: 18:37
 */

//ROLES
define("ROL_USER"       , 1);
define("ROL_DOCTOR"     , 2);
define("ROL_AUXILIAR"   , 3);
define("ROL_ADMIN"      , 4);

//PERMISOS
//permissions 0 son los por defecto del rol
define("PERMISSION_LOGIN"           , 1);
define("PERMISSION_PROFILE"         , 2);
define("PERMISSION_NEWS"            , 3);
define("PERMISSION_RECORD"          , 4);
define("PERMISSION_CHAT"            , 5);
define("PERMISSION_CALENDAR"        , 6);
define("PERMISSION_ROOM"            , 7);
define("PERMISSION_USERS"           , 8);
define("PERMISSION_ROLES"           , 9);
define("PERMISSION_STAFF"           , 10);
define("PERMISSION_PATIENT"         , 11);

class Permission {

    var $id;
    var $name;
    var $timestamp;
    var $creator;
    var $values;
    var $rol;
    var $individual;
    var $deleted = 0;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(PERMISSION_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
            }
        }
    }

    public static function get_all(){
        $permissions = array();
        if($records = db_gets(PERMISSION_TABLE, 'deleted', 0)){
            foreach($records as $record){
                $Permission = new Permission();
                $Permission->loadFromRecord($record);
                $Permission->setValues(Permission::getPermission($Permission->getId(), $Permission->getRol(), $Permission->getValues()));
                $permissions[$Permission->getId()] = $Permission;
            }
        }
        return $permissions;
    }

    public static function get_individual($user_id){
        if($record = db_get(PERMISSION_TABLE, 'individual', $user_id)){
            $Permission = new Permission();
            $Permission->loadFromRecord($record);
            return $Permission;
        }
        return false;
    }

    public function loadFromRecord($record){
        $this->setId            ($record->id);
        $this->setName          (rawurldecode($record->name));
        $this->setTimestamp     ($record->timestamp);
        $this->setCreator       ($record->creator);
        $this->setValues        (json_decode($record->values));
        $this->setRol           ($record->rol);
        $this->setIndividual    ($record->individual);
    }

    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getName(){return $this->name;}
    public function setName($value){$this->name = $value;}

    public function getTimestamp(){return $this->timestamp;}
    public function setTimestamp($value){$this->timestamp = $value;}

    public function getCreator(){return $this->creator;}
    public function setCreator($value){$this->creator = $value;}

    public function getValues(){return $this->values;}
    public function setValues($value){$this->values = $value;}

    public function getRol(){return $this->rol;}
    public function setRol($value){$this->rol = $value;}

    public function getIndividual(){return $this->individual;}
    public function setIndividual($value){$this->individual = $value;}

    public function isDeleted(){return $this->deleted;}
    public function setDeleted($value){$this->deleted = $value;}

    /*
    ###########################################################################################
                                    CHECK PERMS
    ###########################################################################################
    */

    public static function is($rol){
        global $USER;
        return $USER->rol == $rol;
    }

    public static function can_view($name){
        return Permission::can($name, 1);
    }

    public static function can_edit($name){
        return Permission::can($name, 2);
    }

    public static function can_manage($name){
        return Permission::can($name, 3);
    }

    public static function can($name, $level = 1){
        global $USER;
        return $USER->perms->{$name} >= $level;
    }

    public static function loadUserPerms($force = false){
        global $USER;

        if(!isset($USER->perms) || $USER->perms === false || $force){
            $rol_perms = ($USER->rol_perms) ? $USER->rol_perms : $USER->rol;
            $perms     = Permission::getPermission($rol_perms, $USER->rol);

            session_start();
                $USER->perms      = $perms;
                $_SESSION['user'] = serialize($USER);
            session_write_close();
        }
        return true;
    }

    public static function getPermission($perm_id, $rol = 1, $values = false){
        $permissions = Permission::structurePermissions();
        $perms       = new StdClass();

        if(!$values){
            $Permission  = new Permission($perm_id);
            $values      = $Permission->getValues();
        }

        foreach($permissions as $value){
            $perms->{$value['id']} = ($values)
                ? $values->{$value['id']}
                : $value['default'][$rol];
        }
        return $perms;
    }

    public static function structureRoles(){
        return array(
            ROL_USER => array(
                'id'   => ROL_USER,
                'name' => 'Pacientes'
            ),
            ROL_DOCTOR => array(
                'id'   => ROL_DOCTOR,
                'name' => 'Personal Sanitario'
            ),
            ROL_AUXILIAR => array(
                'id'   => ROL_AUXILIAR,
                'name' => 'Personal Administrativo'
            ),
            ROL_ADMIN => array(
                'id'   => ROL_ADMIN,
                'name' => 'Administradores sistema'
            )
        );
    }

    public static function structurePermissions(){
        return array(
            PERMISSION_LOGIN => array(
                'id'      => PERMISSION_LOGIN,
                'name'    => 'Logearse',
                'levels'  => array(
                    '1' => 'Si'
                ),
                'default' => array(
                    ROL_USER     => 1,
                    ROL_DOCTOR   => 1,
                    ROL_AUXILIAR => 1,
                    ROL_ADMIN    => 1
                ),
                'icon'  => 'fa-sign-in'
            ),
            PERMISSION_PROFILE => array(
                'id'      => PERMISSION_PROFILE,
                'name'    => 'Perfil del usuario',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar Suyo',
                    '3' => 'Editar Todos'
                ),
                'default' => array(
                    ROL_USER     => 2,
                    ROL_DOCTOR   => 2,
                    ROL_AUXILIAR => 3,
                    ROL_ADMIN    => 3
                ),
                'icon'  => 'fa-male'
            ),
            PERMISSION_NEWS => array(
                'id'      => PERMISSION_NEWS,
                'name'    => 'Noticias',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Crear',
                    '3' => 'Administrar'
                ),
                'default' => array(
                    ROL_USER     => 1,
                    ROL_DOCTOR   => 1,
                    ROL_AUXILIAR => 1,
                    ROL_ADMIN    => 3
                ),
                'icon'  => 'fa-book'
            ),
            PERMISSION_RECORD => array(
                'id'      => PERMISSION_RECORD,
                'name'    => 'Historiales',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar'
                ),
                'default' => array(
                    ROL_USER     => 1,
                    ROL_DOCTOR   => 2,
                    ROL_AUXILIAR => 2,
                    ROL_ADMIN    => 2
                ),
                'icon'  => 'fa-folder-open-o'
            ),
            PERMISSION_CHAT => array(
                'id'      => PERMISSION_CHAT,
                'name'    => 'Mensajeria',
                'levels'  => array(
                    '1' => 'No puede iniciar conversaciones',
                    '2' => 'Hablar relacionados',
                    '3' => 'Hablar cualquiera'
                ),
                'default' => array(
                    ROL_USER     => 2,
                    ROL_DOCTOR   => 2,
                    ROL_AUXILIAR => 3,
                    ROL_ADMIN    => 3
                ),
                'icon'  => 'fa-comments-o'
            ),
            PERMISSION_CALENDAR => array(
                'id'      => PERMISSION_CALENDAR,
                'name'    => 'Agenda',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar',
                ),
                'default' => array(
                    ROL_USER     => 1,
                    ROL_DOCTOR   => 2,
                    ROL_AUXILIAR => 3,
                    ROL_ADMIN    => 3
                ),
                'icon'  => 'fa-calendar'
            ),
            PERMISSION_ROOM => array(
                'id'      => PERMISSION_ROOM,
                'name'    => 'Sala de espera',
                'levels'  => array(
                    '1' => 'Entrar'
                ),
                'default' => array(
                    ROL_USER     => 1,
                    ROL_DOCTOR   => 1,
                    ROL_AUXILIAR => 0,
                    ROL_ADMIN    => 0
                ),
                'icon'  => 'fa-stethoscope'
            ),
            PERMISSION_USERS => array(
                'id'      => PERMISSION_USERS,
                'name'    => 'Gestion de usuarios',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar'
                ),
                'default' => array(
                    ROL_USER     => 0,
                    ROL_DOCTOR   => 0,
                    ROL_AUXILIAR => 1,
                    ROL_ADMIN    => 2
                ),
                'icon'  => 'fa-h-square'
            ),
            PERMISSION_ROLES => array(
                'id'      => PERMISSION_ROLES,
                'name'    => 'Gestion de permisos',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar'
                ),
                'default' => array(
                    ROL_USER     => 0,
                    ROL_DOCTOR   => 0,
                    ROL_AUXILIAR => 0,
                    ROL_ADMIN    => 2
                ),
                'icon'  => 'fa-key'
            ),
            PERMISSION_STAFF => array(
                'id'      => PERMISSION_STAFF,
                'name'    => 'Gestion del personal',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar'
                ),
                'default' => array(
                    ROL_USER     => 0,
                    ROL_DOCTOR   => 0,
                    ROL_AUXILIAR => 2,
                    ROL_ADMIN    => 2
                ),
                'icon'  => 'fa-user-md'
            ),
            PERMISSION_PATIENT => array(
                'id'      => PERMISSION_PATIENT,
                'name'    => 'Gestion de pacientes',
                'levels'  => array(
                    '1' => 'Ver',
                    '2' => 'Editar'
                ),
                'default' => array(
                    ROL_USER     => 0,
                    ROL_DOCTOR   => 0,
                    ROL_AUXILIAR => 2,
                    ROL_ADMIN    => 2
                ),
                'icon'  => 'fa-group'
            )
        );
    }

    /*
    ###########################################################################################
                                    CHECK PERMS
    ###########################################################################################
    */

    public function save(){
        $this->name   = rawurlencode($this->name);
        $this->values = json_encode($this->values);
        if($this->getId()){
            if(!db_update(PERMISSION_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(PERMISSION_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }
        $this->name   = rawurldecode($this->name);
        $this->values = json_decode($this->values);
        return $this->getId();
    }

    public function delete(){
        $this->setDeleted(1);
        if($this->save()){
            $sql = 'UPDATE ' . USER_TABLE . ' SET ´rol_perms´ = 0 WHERE ´rol_perms´ = :p1';
            $response = db_query($sql, array(':p1' => $this->getId()),' update');
            return true;
        }
        return false;
    }
} 