<?php
namespace App\Controllers;
use Clases\Usuario;
use Clases\Empleados;
use Clases\Personas;


use App\Models\Cliente;

use Clases\Token;
use Clases\Archivos;

class ClienteController {
    public $datos = array ('datos' => '.');
    
    public function registro($request, $response, $args)
    {

        $parsedBody = $request->getParsedBody();
        $nombre = $parsedBody['nombre'];
        $usuario = $parsedBody['usuario'];
        $apellido = $parsedBody['apellido'];
        $clave = $parsedBody['clave'];

        

        // echo $nombre . $apellido.$tipo;
        
        if($nombre != null && $apellido != null){
            
            
            
            $usuarioCreado = Usuario::CrearUsuario($usuario,$nombre,$apellido,$clave,'',"cliente");

            $clienteCreado = new Personas($usuarioCreado->usuario,$usuarioCreado->nombre,$usuarioCreado->apellido,$usuarioCreado->clave);
            $token = Usuario::Login($usuario,$clave,$usuarioCreado->clave,$usuarioCreado->imagenNombre,"cliente");

            $cliente = new Cliente;
            $cliente->nombre = $clienteCreado->nombre;
            $cliente->apellido = $clienteCreado->apellido;
            $cliente->usuario = $clienteCreado->usuario;
            $cliente->clave = $clienteCreado->clave;
        

            $rta = $cliente->save();
            
            $datos['datos'] = 'Se creo el cliente correctamente!';
            $datos['token'] = $token;
                // var_dump($usuarioCreado);
                
                // var_dump($user);
            
        }
        else{
            $rta = false;
            $datos['datos'] = "Complete los campos correctamente.";
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function login($request, $response, $args)
    {

        
        $parsedBody = $request->getParsedBody();
        
        $email = $parsedBody['email'];
        $clave = $parsedBody['clave'];
        // $nombre = $parsedBody['nombre'];
        
        $usuario = User::where('email', $email)
        ->first();
        
        
        if($usuario != null){
            
            $token = Usuario::Login($email,$clave,$usuario->password,$usuario->imagenNombre,$usuario->tipo);
            
        }
        else {
            $token = false;
        }
        

        if($token != false)
        {
            $datos['datos'] = 'Login Exitoso.';
            $datos['token'] = $token;
            
        }
        else
        {
            $datos['datos'] = 'Nombre o Clave Incorrecto';                       
        }
        $payload = json_encode($datos);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function update($request, $response, $args)
    {
        $params = (array)$request->getQueryParams();

        $id = $args['id'];
        $email = $params['email'];
        $name = $params['name'];
        // var_dump($request);
        // $parsedBody = $request->getParsedBody();
        // var_dump($params);
        // $email = $parsedBody['email'];
        // $name = $parsedBody['name'];

        $user = User::find($id);
        if($user){
            $user->email = $email;
            $user->name = $name;
            
            if($user->save()){

                $datos['datos'] = "Se modifico el usuario correctamente.";
                
            }
            else{
                $datos['datos'] = "Error al modificar datos";
            }
            
        }
        else{
            $datos['datos'] = "Error. ID no encontrado";
        }

        // $rta = $user->save();

        $response->getBody()->write(json_encode($datos));
        return $response;
    }

    public function delete($request, $response, $args)
    {
        $id = $args['id'];
        $parsedBody = $request->getParsedBody();
        
        // $rta = User::where('email', $email)->first();

        $user = User::find($id);
        if($user){
            if($user->delete()){
                $datos['datos'] = "Se elimino el usuario correctamente.";
            }
        }
        else{
            $datos['datos'] = "Error. ID no encontrado";
        }
        

        $response->getBody()->write(json_encode($datos));
        return $response;
        // getAll();
    }
    
    // $respuesta = new stdClass;
    // $respuesta->success = true;

    // $respuesta->data = $datos; 

    
}