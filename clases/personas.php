<?php
namespace Clases;
class Personas{
    public $nombre;
    public $apellido;
    public $usuario;
    public $clave;

    public function __construct($usuario,$nombre,$apellido,$clave){
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->usuario = $usuario;
        $this->clave = $clave;
    }
    
}


?>