<?php

class Medicine {
    var $id;
    var $patient;
    var $doctor;
    var $timestamp;
    var $medicine;
    var $start;
    var $end;
    var $interval;
    var $stop;
    var $user_stop;
    var $other;

    public function __construct($id = false) {
        if($id) {
            if($record = db_get(MEDICINE_TABLE, 'id', $id)){
                $this->loadFromRecord($record);
                return $this;
            }
        }
        return false;
    }

    public function loadFromRecord($record){
        $this->setId        ($record->id);
        $this->setPatient   ($record->patient);
        $this->setDoctor    ($record->doctor);
        $this->setMedicine  ($record->medicine);
        $this->setTimestamp ($record->timestamp);
        $this->setStart     ($record->start);
        $this->setEnd       ($record->end);
        $this->setInterval  ($record->interval);
        $this->setStop      (rawurldecode($record->stop));
        $this->setStop_user ($record->stop_user);

        $this->loadExtraInfo();
    }

    public function loadExtraInfo(){
        $this->other = new StdClass();

        $interval = explode('#', $this->interval);
        $this->other->interval_text = $interval[0] . ' cada ' . $interval[1] . ' ';
        switch($interval[2]){
            case 'h': $this->other->interval_text .= 'hora/s';break;
            case 'd': $this->other->interval_text .= 'dia/s';break;
            case 'w': $this->other->interval_text .= 'semana/s';break;
            case 'm': $this->other->interval_text .= 'mes/es';break;
            case 'y': $this->other->interval_text .= 'año/s';break;
        }

        $this->other->name = Medicine::getName($this->medicine);

        $this->timestamp    = explode(' ', $this->timestamp);
        $this->timestamp    = $this->timestamp[0];
        $this->start        = explode(' ', $this->start);
        $this->start        = $this->start[0];
        $this->end          = explode(' ', $this->end);
        $this->end          = $this->end[0];

        $this->other->finished = $this->end != '0000-00-00';

        $this->other->status = 'Activo';

        if($this->other->finished){
            $this->other->status = 'Finalizado (' . $this->end .')';
        }else if(strtotime($this->start) > strtotime('now')){
            $this->other->status = 'Sin empezar';
        }
    }

    public static function getByUser($user_id = false){
        global $USER;
        if(!$user_id)
            $user_id = $USER->id;

        $medicines = array();

        $sql = 'SELECT * FROM ' . MEDICINE_TABLE . ' WHERE patient = :p1 ORDER BY start DESC';
        if($records = db_query($sql, array(':p1' => $user_id))){
            foreach($records as $record){
                $Medicine = new Medicine();
                $Medicine->loadFromRecord($record);
                array_push($medicines, $Medicine);
            }
        }

        return $medicines;
    }
    /*
    ###########################################################################################
                                    GETTER AND SETTER
    ###########################################################################################
    */

    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}

    public function getPatient(){return $this->patient;}
    public function setPatient($value){$this->patient = $value;}

    public function getDoctor(){return $this->doctor;}
    public function setDoctor($value){$this->doctor = $value;}

    public function getTimestamp(){return $this->timestamp;}
    public function setTimestamp($value){$this->timestamp = $value;}

    public function getMedicine(){return $this->medicine;}
    public function setMedicine($value){$this->medicine = $value;}

    public function getStart(){return $this->start;}
    public function setStart($value){$this->start = $value;}

    public function getEnd(){return $this->end;}
    public function setEnd($value){$this->end = $value;}

    public function getInterval(){return $this->interval;}
    public function setInterval($value){$this->interval = $value;}

    public function getStop(){return $this->stop;}
    public function setStop($value){$this->stop = $value;}

    public function getStop_user(){return $this->stop_user;}
    public function setStop_user($value){$this->stop_user = $value;}

    /*
    ###########################################################################################
                                    PROCESS
    ###########################################################################################
    */

    public function save(){
        $this->stop = rawurlencode($this->stop);
        if($this->getId()){
            if(!db_update(MEDICINE_TABLE, $this)){
                return false;
            }
        }else{
            if($id = db_insert(MEDICINE_TABLE, $this)){
                $this->setId($id);
            }else{
                return false;
            }
        }
        $this->stop = rawurldecode($this->stop);

        return $this->getId();
    }

    public function delete(){
        if($this->getId()){
            return db_delete(MEDICINE_TABLE, $this->getId());
        }
    }


    public static function getName($id){
        $medicine = Medicine::getAll()[$id - 1];
        return $medicine['name'];
    }

    public static function getAll(){
        return array(
            '0' => array('id' => 1, 'name' => 'Abacavir'),
            '1' => array('id' => 2, 'name' => 'Abacavir / Lamivudina'),
            '2' => array('id' => 3, 'name' => 'Abacavir / Lamivudina / Zidovudina'),
            '3' => array('id' => 4, 'name' => 'ABC/3TC'),
            '4' => array('id' => 5, 'name' => 'Aciclovir'),
            '5' => array('id' => 6, 'name' => 'Albendazol'),
            '6' => array('id' => 7, 'name' => 'AMD-070'),
            '7' => array('id' => 8, 'name' => 'Amdoxovir'),
            '8' => array('id' => 9, 'name' => 'Anfotericina B'),
            '9' => array('id' => 10, 'name' => 'Aptivus'),
            '10' => array('id' => 11, 'name' => 'Astodrímero'),
            '11' => array('id' => 12, 'name' => 'Atazanavir'),
            '12' => array('id' => 13, 'name' => 'Atripla'),
            '13' => array('id' => 14, 'name' => 'Azitromicina'),
            '14' => array('id' => 15, 'name' => 'BMS-663068'),
            '15' => array('id' => 16, 'name' => 'BMS-986001'),
            '16' => array('id' => 17, 'name' => 'Boceprevir'),
            '17' => array('id' => 18, 'name' => 'Carbopol 974P '),
            '18' => array('id' => 19, 'name' => 'Carragaén'),
            '19' => array('id' => 20, 'name' => 'Carragenina lambda'),
            '20' => array('id' => 21, 'name' => 'Cenicriviroc'),
            '21' => array('id' => 22, 'name' => 'Ciprofloxacina'),
            '22' => array('id' => 23, 'name' => 'Claritromicina'),
            '23' => array('id' => 24, 'name' => 'Clindamicina'),
            '24' => array('id' => 25, 'name' => 'Clorhidrato de etambutol'),
            '25' => array('id' => 26, 'name' => 'Clorhidrato de moxifloxacina'),
            '26' => array('id' => 27, 'name' => 'clorhidrato de rilpivirina'),
            '27' => array('id' => 28, 'name' => 'Clorhidrato de valaciclovir'),
            '28' => array('id' => 29, 'name' => 'Clorhidrato de valganciclovir'),
            '29' => array('id' => 30, 'name' => 'Clotrimazol '),
            '30' => array('id' => 31, 'name' => 'Combivir'),
            '31' => array('id' => 32, 'name' => 'Complera'),
            '32' => array('id' => 33, 'name' => 'Crixivan'),
            '33' => array('id' => 34, 'name' => 'Cytovene IV'),
            '34' => array('id' => 35, 'name' => 'Dapivirina'),
            '35' => array('id' => 36, 'name' => 'Darunavir'),
            '36' => array('id' => 37, 'name' => 'Delavirdina'),
            '37' => array('id' => 38, 'name' => 'Dexelvucitabina'),
            '38' => array('id' => 39, 'name' => 'Didanosina'),
            '39' => array('id' => 40, 'name' => 'Dolutegravir'),
            '40' => array('id' => 41, 'name' => 'Edurant'),
            '41' => array('id' => 42, 'name' => 'Efavirenz'),
            '42' => array('id' => 43, 'name' => 'Efavirenz / Emtricitabina / Fumarato de Disoproxilo de Tenofovir'),
            '43' => array('id' => 44, 'name' => 'efavirenz/emtricitabina/tenofovir'),
            '44' => array('id' => 45, 'name' => 'efavirenz/emtricitabina/tenofovir DF'),
            '45' => array('id' => 46, 'name' => 'EFV/FTC/TDF'),
            '46' => array('id' => 47, 'name' => 'Elvitegravir'),
            '47' => array('id' => 48, 'name' => 'Elvitegravir / Cobicistat / Emtricitabina / Fumarato de Disoproxilo de Tenofovir'),
            '48' => array('id' => 49, 'name' => 'Elvucitabina'),
            '49' => array('id' => 50, 'name' => 'Emtricitabina'),
            '50' => array('id' => 51, 'name' => 'emtricitabina/clorhidrato de rilpivirina/fumarato de disoproxilo de tenofovir'),
            '51' => array('id' => 52, 'name' => 'Emtricitabina / Fumarato de Disoproxilo de Tenofovir'),
            '52' => array('id' => 53, 'name' => 'Emtricitabina / Rilpivirina / Fumarato de Disoproxilo de Tenofovir'),
            '53' => array('id' => 54, 'name' => 'Emtricitabina/rilpivirina/tenofovir'),
            '54' => array('id' => 55, 'name' => 'Emtricitabina/rilpivirina/tenofovir'),
            '55' => array('id' => 56, 'name' => 'Emtriva'),
            '56' => array('id' => 57, 'name' => 'Enfuvirtida'),
            '57' => array('id' => 58, 'name' => 'Engerix-B'),
            '58' => array('id' => 59, 'name' => 'Epivir'),
            '59' => array('id' => 60, 'name' => 'Epzicom'),
            '60' => array('id' => 61, 'name' => 'Estavudina'),
            '61' => array('id' => 62, 'name' => 'Etravirina'),
            '62' => array('id' => 63, 'name' => 'EVG/COBI/FTC/TDF'),
            '63' => array('id' => 64, 'name' => 'Famciclovir'),
            '64' => array('id' => 65, 'name' => 'Flucitosina'),
            '65' => array('id' => 66, 'name' => 'Fluconazol'),
            '66' => array('id' => 67, 'name' => 'Fosamprenavir'),
            '67' => array('id' => 68, 'name' => 'Foscarnet sódico'),
            '68' => array('id' => 69, 'name' => 'Fosfato de primaquina'),
            '69' => array('id' => 70, 'name' => 'FTC/RPV/TDF'),
            '70' => array('id' => 71, 'name' => 'Fumarato de Disoproxilo de Tenofovir'),
            '71' => array('id' => 72, 'name' => 'Fuzeon'),
            '72' => array('id' => 73, 'name' => 'Ganciclovir'),
            '73' => array('id' => 74, 'name' => 'Ganciclovir sódico'),
            '74' => array('id' => 75, 'name' => 'Ibalizumab'),
            '75' => array('id' => 76, 'name' => 'Imiquimod'),
            '76' => array('id' => 77, 'name' => 'INCB-9471'),
            '77' => array('id' => 78, 'name' => 'Indinavir'),
            '78' => array('id' => 79, 'name' => 'Intelence'),
            '79' => array('id' => 80, 'name' => 'Interferón pegilado alfa-2a'),
            '80' => array('id' => 81, 'name' => 'Interferón pegilado alfa-2b'),
            '81' => array('id' => 82, 'name' => 'Invirase'),
            '82' => array('id' => 83, 'name' => 'Isentress'),
            '83' => array('id' => 84, 'name' => 'Isoniazida'),
            '84' => array('id' => 85, 'name' => 'Itraconazol'),
            '85' => array('id' => 86, 'name' => 'Kaletra'),
            '86' => array('id' => 87, 'name' => 'Lamivudina'),
            '87' => array('id' => 88, 'name' => 'Lamivudina / Zidovudina'),
            '88' => array('id' => 89, 'name' => 'Lersivirina'),
            '89' => array('id' => 90, 'name' => 'Levofloxacina'),
            '90' => array('id' => 91, 'name' => 'Lexiva'),
            '91' => array('id' => 92, 'name' => 'Lopinavir / Ritonavir'),
            '92' => array('id' => 93, 'name' => 'Maraviroc'),
            '93' => array('id' => 94, 'name' => 'Miconazol'),
            '94' => array('id' => 95, 'name' => 'MPC-4326'),
            '95' => array('id' => 96, 'name' => 'Nelfinavir'),
            '96' => array('id' => 97, 'name' => 'Nevirapina'),
            '97' => array('id' => 98, 'name' => 'Nitrato de butoconazol'),
            '98' => array('id' => 99, 'name' => 'Norvir'),
            '99' => array('id' => 100, 'name' => 'PC-515'),
            '100' => array('id' => 101, 'name' => 'Pirazinamida'),
            '101' => array('id' => 102, 'name' => 'Pirimetamina'),
            '102' => array('id' => 103, 'name' => 'potasio de raltegravir'),
            '103' => array('id' => 104, 'name' => 'Prezista'),
            '104' => array('id' => 105, 'name' => 'PRO-140'),
            '105' => array('id' => 106, 'name' => 'PRO-2000'),
            '106' => array('id' => 107, 'name' => 'Profármaco del BMS-626529'),
            '107' => array('id' => 108, 'name' => 'QUAD'),
            '108' => array('id' => 109, 'name' => 'Racivir'),
            '109' => array('id' => 110, 'name' => 'RAL'),
            '110' => array('id' => 111, 'name' => 'Raltegravir'),
            '111' => array('id' => 112, 'name' => 'Recombivax HB'),
            '112' => array('id' => 113, 'name' => 'Rescriptor'),
            '113' => array('id' => 114, 'name' => 'Retrovir'),
            '114' => array('id' => 115, 'name' => 'Reyataz'),
            '115' => array('id' => 116, 'name' => 'Ribarivina'),
            '116' => array('id' => 117, 'name' => 'Rifabutina'),
            '117' => array('id' => 118, 'name' => 'Rifampicina'),
            '118' => array('id' => 119, 'name' => 'Rilpivirina'),
            '119' => array('id' => 120, 'name' => 'Ritonavir'),
            '120' => array('id' => 121, 'name' => 'RPV'),
            '121' => array('id' => 122, 'name' => 'Saquinavir'),
            '122' => array('id' => 123, 'name' => 'Selzentry'),
            '123' => array('id' => 124, 'name' => 'S/GSK1265744'),
            '124' => array('id' => 125, 'name' => 'SPL-7013'),
            '125' => array('id' => 126, 'name' => 'Stribild'),
            '126' => array('id' => 127, 'name' => 'Sulfadiazina'),
            '127' => array('id' => 128, 'name' => 'Sulfametoxazol - Trimetoprima'),
            '128' => array('id' => 129, 'name' => 'sulfato de abacavir/lamivudina'),
            '129' => array('id' => 130, 'name' => 'Sustiva'),
            '130' => array('id' => 131, 'name' => 'Telaprevir'),
            '131' => array('id' => 132, 'name' => 'Tenofovir/alafenamida'),
            '132' => array('id' => 133, 'name' => 'Tenofovir (microbicida) '),
            '133' => array('id' => 134, 'name' => 'Terconazol'),
            '134' => array('id' => 135, 'name' => 'Tipranavir'),
            '135' => array('id' => 136, 'name' => 'Tivicay'),
            '136' => array('id' => 137, 'name' => 'Trizivir'),
            '137' => array('id' => 138, 'name' => 'Truvada'),
            '138' => array('id' => 139, 'name' => 'Twinrix'),
            '139' => array('id' => 140, 'name' => 'Vacuna contra la hepatitis A y la hepatitis B'),
            '140' => array('id' => 141, 'name' => 'Vacuna contra la hepatitis B'),
            '141' => array('id' => 142, 'name' => 'Vacuna de virus vivos contra la varicela'),
            '142' => array('id' => 143, 'name' => 'Varivax'),
            '143' => array('id' => 144, 'name' => 'Vicriviroc'),
            '144' => array('id' => 145, 'name' => 'Videx'),
            '145' => array('id' => 146, 'name' => 'Videx EC'),
            '146' => array('id' => 147, 'name' => 'Viracept'),
            '147' => array('id' => 148, 'name' => 'Viramune'),
            '148' => array('id' => 149, 'name' => 'Viramune XR'),
            '149' => array('id' => 150, 'name' => 'Viread'),
            '150' => array('id' => 151, 'name' => 'Voriconazol'),
            '151' => array('id' => 152, 'name' => 'Zerit'),
            '152' => array('id' => 153, 'name' => 'Ziagen'),
            '153' => array('id' => 154, 'name' => 'Zidovudina')
        );
    }
} 