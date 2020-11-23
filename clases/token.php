<?php
namespace Clases;

use \Firebase\JWT\JWT;
class Token{

    public static function crearToken($dato){
        $retorno = false;
        $key = "tpComanda";

        $payload = array(
            "usuario" => $dato['usuario'],
            "clave" => $dato['clave'],
            "tipo" => $dato['tipo']

        );
        $retorno = JWT::encode($payload, $key);
        return $retorno;
        
        
    }
    public static function VerificarToken($token){
        $key = "tpComanda";
        $retorno = false;
        try {
            $retorno = JWT::decode($token, $key, array('HS256'));
        }
        catch (Exception $e) {
            echo "error";
            $retorno = false;
        }
        return $retorno;
    }
}








?>