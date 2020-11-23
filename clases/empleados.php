<?php
namespace Clases;
class Empleados{
    public $nombre;
    public $apellido;
    public $tipo;
    public $disponible;
    
    public function __construct($nombre,$apellido,$tipo){
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->disponible = true;
        $this->tipo = $this->asignarTipo($tipo);


        
    }
    public function asignarTipo($tipo){
        switch ($tipo) {
            case 'bartender':
                return $tipo;
                break;
            case 'mozo':
                return $tipo;
                break;

            case 'cervecero':
                return $tipo;
                break;
            case 'cocinero':
                return $tipo;
                break;
            default:
                return null;
                break;
        }
    }
}


?>