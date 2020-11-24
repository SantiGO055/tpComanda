<?php
namespace App\Controllers;
use Clases\Usuario;
use Clases\Auto;
use Clases\Servicio;
use Clases\Socios;
use Clases\Personas;
use Clases\Token;
use Clases\Archivos;

use App\Models\Socio;
use App\Models\Empleado;
use App\Models\EstadoEmpleado;
use App\Models\Item;
use App\Models\Turno;
use App\Models\Pedido;



class SocioController {
    public $datos = array ('datos' => '.');
    public static function getAllCount () {
        $rta = Socio::get()->count(); //select * from users
        // $rta = User::find(1);
        // $rta = User::where('id', '>',  0)
        // ->where('campo', 'operador', 'valor')        
        // ->get();

        return json_encode($rta);
    }
    public function getAllVehiculos ($request, $response, $args) {
        $rta = Vehiculo::get();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getUserEmail($request, $response, $args, $email){
        // $rta = User::get(); //select * from users
        $rta = User::where('email', $email)
        ->first();
        return $rta;
    }
    
    public function registro($request, $response, $args)
    {

        $parsedBody = $request->getParsedBody();
        $nombre = $parsedBody['nombre'];
        $usuario = $parsedBody['usuario'];
        $apellido = $parsedBody['apellido'];
        $clave = $parsedBody['clave'];
        $countSocios = SocioController::getAllCount();
        
        // echo $nombre . $apellido.$tipo;
        
        if($nombre != null && $apellido != null){
            
            if($countSocios < 3){

                
                $rta = Socio::where('nombre', $nombre)->where('apellido',$apellido)->where('usuario',$usuario)->first();
                // var_dump($rta);
                // $claveEncriptada = Persona::encriptarContraseña($clave);
                // var_dump( $datos['datos']);
                
                
                // var_dump($usuarioCreado);
                if($rta == null)
                {
                    $usuarioCreado = Usuario::CrearUsuario($usuario,$nombre,$apellido,$clave,'',"socio");

                    $socioCreado = new Personas($usuarioCreado->usuario, $usuarioCreado->nombre,$usuarioCreado->apellido, $usuarioCreado->clave);

                    // $token = Usuario::Login($email,$clave,$usuario->password,$usuario->imagenNombre,$usuario->tipo);

                    // var_dump($usuarioCreado);
                    $socio = new Socio;
                    $socio->nombre = $socioCreado->nombre;
                    $socio->apellido = $socioCreado->apellido;
                    $socio->usuario = $socioCreado->usuario;
                    $socio->clave = $socioCreado->clave;
                    

                    $rta = $socio->save();

                    $token = Usuario::Login($usuario,$clave,$usuarioCreado->clave,$usuarioCreado->imagenNombre,"socio");

                    $datos['datos'] = 'Se creo el socio correctamente!';
                    $datos['token'] = $token;
                    // var_dump($user);
                }
                else
                {
                    $rta = false;
                    $datos['datos'] = "Error, socio existente";
                    
                }
            }
            else{
                $datos['datos'] = "Se dio de alta la cantidad maxima de socios";
            }
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

    public function getEmpleados($request, $response, $args){
        $empleados = Empleado::get()->toArray();
        $datos['datos'] = " ";
        
        if($empleados != null){
            
            foreach ($empleados as $key => $value) {
                $estadoEmpleado = EstadoEmpleado::where('id',$value['id_estado'])->first();
                // $estadoDePedido = EstadoPedido::where('id',$value['id_estado'])->first();
                // $cliente = Cliente::where('id',$value['id_cliente'])->first();
                // $empleado = Empleado::where('id',$value['id_empleado'])->first();
                // $mesa = Mesa::where('id',$value['id_mesa'])->first();
                // $estadoMesa = EstadoMesa::where('id',$mesa->id_estado)->first();
                

                $datos['datos'] .= "<br> Empleado: Nombre: " . $value['nombre'] . " - Apellido: " . $value['apellido']
                . "<br> - Fecha de ingreso:" . "  " . $value['created_at']
                . "<br> - Cantidad de operaciones:" . "  " . $value['operaciones']
                . "<br> - Estado del empleado:" . "  " . $estadoEmpleado->descripcion. "<br><br>" ;
                // . "<br> - Precio: " . $value['precio']
                // . "<br> - Cliente: " . $cliente->nombre . ", " . $cliente->apellido
                // . "<br> - Empleado: " . $empleado->nombre . ", " . $empleado->apellido
                // . "<br> - Mesa: " . " Descripcion: " . $estadoMesa->descripcion . " - Codigo: " . $mesa->codigo . "<br><br>";
            }
            
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
    public function isEmail($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }
        else{
            return $email;
        }
    }

    // $respuesta = new stdClass;
    // $respuesta->success = true;

    // $respuesta->data = $datos; 

    
}