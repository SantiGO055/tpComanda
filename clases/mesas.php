<?php
namespace Clases;
class Mesas{
    
    public $descripcion;
    public $codigo;

    public function __construct($descripcion){
        
        $this->descripcion = $descripcion;
        $this->codigo = $this->generarCodigo();
    }
    public function generarCodigo(){
        return substr(uniqid('', true), -5);
        // return substr(md5(time()),0,5);
    }
}

?>