<?php
namespace Clases;
use Clases\Token;
use Clases\Archivos;



class Usuario{
    public $usuario;
    public $nombre;
    public $apellido;
    public $clave;
    public $imagenNombre;
    public $tipo;

    public function __construct($usuario,$nombre,$apellido, $clave, $imagenNombre,$tipo)
    {
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->clave = $clave;
        $this->imagenNombre = $imagenNombre;
        $this->tipo = $tipo;
    }
    
    

    public static function Login($usuario,$clave,$claveBase,$imagenNombre,$tipo){
        $retorno = false;
        
        if ( Usuario::verificarContraseña($clave,$claveBase)) {
            $usuario = array('usuario' => $usuario, 'clave' => $claveBase,'tipo'=>$tipo);
            //Usuario::verificarContraseña($clave,$usuario['clave']);

            $token = Token::crearToken($usuario);
            
            return $token;
        // break;
        }
        else{
            return false;
        }
    }

    public static function encriptarContraseña($clave){
        return password_hash($clave, PASSWORD_DEFAULT);
    }

    public static function verificarContraseña($clave,$hash){
        // echo $clave;
        // echo $hash;
        return password_verify($clave, $hash);
    }

    

    public static function CrearUsuario($usuario,$nombre,$apellido,$clave,$imagenNombre,$tipo){
        $retorno = false;
        
        $claveEncriptada = Usuario::encriptarContraseña($clave);

        $imagenNombre = "";
        $usuario = new Usuario($usuario,$nombre,$apellido,$claveEncriptada,'./imagenes/'.$imagenNombre,$tipo);
        
        return $usuario;
    }
    
    public static function asignarFotoNueva($email,$foto){
        Archivos::leerJson('./users.json',$listaUsuarios);
        //var_dump($listaUsuarios);
        $nombreFoto = $_FILES["foto"]["name"];
        for ($i=0; $i < count($listaUsuarios); $i++) { 
            if ($listaUsuarios[$i]['email'] === $email) {
                //var_dump($nombreFoto);
                //$pathMover = "./imagenes/" . $listaUsuarios[$i]['imagenNombre'];

                Archivos::moverImagen("./imagenes/imagen" . $listaUsuarios[$i]['imagenNombre'] , "./backup/".$listaUsuarios[$i]['imagenNombre']);
                $usuarioAux = new Usuario($email,$listaUsuarios[$i]['clave'],$nombreFoto);
                
                Archivos::modificarJson("./users.json",$i,"imagenNombre",$nombreFoto);
                $retorno = true;
                return $retorno;
            }
            else{
                $retorno = false;
            }
        }

        
    }



}


?>