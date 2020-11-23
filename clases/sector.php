<?php

// tragos y vinos
// barra de cerveza artesanal
// cocina
// candy bar de postres

namespace Clases;
class Sector{
    public $descripcion;
    
    public $tipo;
    
    
    
    public function __construct($nombre,$apellido,$tipo){
        $this->_nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo = $tipo;
        
    }
    


}


?>