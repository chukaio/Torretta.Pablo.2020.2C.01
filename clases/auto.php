<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class auto
{
    //Atributos
    private $_patente;
    private $_fecha_ingreso;
    private $_email;

    //Constructor
    public function __construct($patente, $fecha_ingreso=null, $email=null)
    {
        $this->_patente = $patente;
        $this->_fecha_ingreso = date("H-i-s");
        $this->_email = auto::getEmail();
    }

    //Metodos
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            return "La propiedad \"" . $name . "\" no existe.<br/>";
        }
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            echo "La propiedad \"" . $name . "\" no existe.<br/>";
        }
    }

    public static function getEmail(){
        $keyJWT = "primerparcial";
        $_SERVER['HTTP_TOKEN'];
        $payload = array(
            "email" => "",
        );
        try {
            $payLoad=JWT::decode($_SERVER['HTTP_TOKEN'],$keyJWT);
        } catch (\Throwable $th) {
            //$payLoad->email="";
            //return return $payLoad->email;
        }

        return $payLoad[0]['email'];
    }

    public function toJson()
    {
        $flag = new stdClass();
        if($this != null)
        {
            $flag->patente = $this->_patente;
            $flag->fecha_ingreso = $this->_fecha_ingreso;
            $flag->email = $this->_email;
        }
        return json_encode($flag);
    }

    public function saveFile() : bool
    {
        $path = "./archivos/autos.json";
        $flag = false;
        $fileStream = fopen($path,"a");

        if($fileStream != false) 
        {
            if(fwrite($fileStream, $this->toJson()."\r\n")) {
                $flag = true;
            }
            fclose($fileStream); 
        }

        return $flag;
    }

    public static function GetAll()
    {
        $listAutos = array();
        $path = "./archivos/autos.json";

        if(file_exists($path)) { 
            $fileStream = fopen($path, "r"); 
            if($fileStream != false) {
                while(!feof($fileStream)) {
                    $linea = trim(fgets($fileStream)); 
                    if($linea != "") 
                    { 
                        $auxJWT = json_decode($linea); 
                        $auxObjAuto = new auto($auxJWT->patente);
                        array_push($listAutos, $auxObjAuto);
                    }
                }
                fclose($fileStream);
            }
        }
        return $listAutos;
    }

}
