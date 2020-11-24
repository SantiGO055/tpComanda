<?php
namespace App\Controllers;
use Clases\Usuario;
use Clases\Empleados;

use App\Models\Empleado;

use App\Models\Turno;
use Clases\Token;
use Clases\Archivos;

class EmpleadoController {
    public $datos = array ('datos' => '.');
    
    
    public function registro($request, $response, $args)
    {

        $parsedBody = $request->getParsedBody();
        $nombre = $parsedBody['nombre'];
        $apellido = $parsedBody['apellido'];
        $tipo = $parsedBody['tipo'];

        // echo $nombre . $apellido.$tipo;
        
        if($nombre != null && $apellido != null && $tipo != null){
            
            
            $rta = Empleado::where('nombre', $nombre)->where('apellido',$apellido)->where('tipo',$tipo)->first();
            // var_dump($rta);
            // $claveEncriptada = Persona::encriptarContraseña($clave);
            // var_dump( $datos['datos']);
            
            
            // var_dump($usuarioCreado);
            if($rta == null)
            {

                $empleadoCreado = new Empleados($nombre,$apellido,$tipo);
                if($empleadoCreado->tipo == null){
                    $datos['datos'] = "Ingrese un tipo de empleado correcto! 'bartender', 'mozo', 'cervecero' o 'cocinero'";
                    
                }
                else{
                    $empleado = new Empleado;
                    $empleado->nombre = $empleadoCreado->nombre;
                    $empleado->apellido = $empleadoCreado->apellido;
                    $empleado->tipo = $empleadoCreado->tipo;
                    $empleado->disponible = $empleadoCreado->disponible;
                    $empleado->operaciones = 0;
                    $empleado->id_estado = 1;
                    if($empleadoCreado->tipo == "bartender"){
                        $empleado->id_sector = 1;
                    }
                    if($empleadoCreado->tipo == "cervecero"){
                        $empleado->id_sector = 2;
                    }
                    if($empleadoCreado->tipo == "cocinero"){
                        $empleado->id_sector = 3;
                    }
                    if($empleadoCreado->tipo == "mozo"){
                        $empleado->id_sector = 4;
                    }
                    
                
                    $rta = $empleado->save();
                    
                    $datos['datos'] = 'Se creo el empleado correctamente!';
                }
                // var_dump($usuarioCreado);
                
                // var_dump($user);
            }
            else
            {
                $rta = false;
                $datos['datos'] = "Error, empleado existente";
                
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
    

    public function getStats($request, $response, $args){
        // $rta = User::get(); //select * from users
        // $token = $request->getHeader('token');
        
        // $user = Token::VerificarToken($token[0]);
        // var_dump($user);
        // $tipo = $args['tipo'];
        // var_dump($tipo);
        if($args != null){
            $tipo = $args['tipo'];
            $rta = Service::where('tipo', $tipo)
            ->get();
        }
        else{
            $rta = Service::get();
        }
        // if($vehiculo != null){
        //     $rta = $vehiculo;
        // }
        // else{
            // }
        // $rta = array( 'datos' => 'No se encontro el vehiculo con la patente ' . $patente);
        $payload = json_encode($rta);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function altaServicio($request, $response, $args){
        
        $parsedBody = $request->getParsedBody();
        $id = $parsedBody['id']; //int
        $tipo = $parsedBody['tipo']; //int -> 10000, 20000, 50000
        $precio = $parsedBody['precio']; //double
        $demora = $parsedBody['demora']; //date
        
        $servicioBase = Service::where('id', $id)->first();
        
        // var_dump( $datos['datos']);
        $servicioCreado = new Servicio($id,$tipo,$precio,$demora);
        // var_dump($usuarioCreado);
        if($servicioBase == null)
        {
            $servicio = new Service;
            $servicio->id = $servicioCreado->id;
            $servicio->tipo = $servicioCreado->tipo;
            $servicio->precio = $servicioCreado->precio;
            $servicio->demora = $servicioCreado->demora;
            $rta = $servicio->save();
            $datos['datos'] = 'Se creo el servicio correctamente!';
            // var_dump($user);
        }
        else
        {
            $rta = false;
            $datos['datos'] = "Error al crear servicio, id existente";
            
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    // public function altaTurno($request, $response, $args){
        
    //     /**
    //      * (GET) turno: Se recibe patente y fecha (día) y se debe guardar en el archivo turnos.xxx, fecha,
    //         *  patente, marca, modelo, precio y tipo de servicio. Si no hay cupo o la patente no existe informar
    //         * cada caso particular.
    //      */
    //     $parsedBody = $request->getParsedBody();
    //     $patente = $parsedBody['patente']; //int
    //     $fecha = $parsedBody['fecha'];
    //     $tipo = $parsedBody['tipo']; //int -> 10000, 20000, 50000
        
    //     $vehiculo = Vehiculo::where('patente', $patente)->first();
    //     $turnoBase = Turno::where('fecha', $fecha)->first();
    //     $tipoServicioBase = Service::where('tipo', $tipo)->first();
        
    //     if($vehiculo != null) //si encontro vehiculo
    //     {
    //         if($turnoBase == null){ //si el turno no existe lo creo
    //             $turno = new Turno;
    //             $turno->fecha = $fecha;
    //             $turno->patente = $vehiculo->patente;
    //             $turno->modelo = $vehiculo->modelo;
    //             $turno->marca = $vehiculo->marca;
    //             $turno->precio = $vehiculo->precio;
    //             $turno->tipo = $tipoServicioBase->tipo;
    //             $rta = $turno->save();
    //             $datos['datos'] = 'Se creo el turno correctamente!';
    //         }
    //         else{ //como existe el turno no lo creo
    //             $datos['datos'] = 'No hay disponibilidad de turno para la fecha ' . $fecha;
    //         }
    //     }
    //     else
    //     {
    //         $rta = false;
    //         $datos['datos'] = "No se encuentra el vehiculo";
            
    //     }
    //     $payload = json_encode($datos);
    //     $response->getBody()->write($payload);
        
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }


    // public function add($request, $response, $args)
    // {
    //     $user = new User;
    //     $user->name = "Juan";
    //     $user->email = "Juan@mail.com";
    //     $user->password = "sdxdsdsds";

    //     $rta = $user->save();

    //     $response->getBody()->write(json_encode($rta));
    //     return $response;
    // }

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