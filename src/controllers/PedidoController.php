<?php
namespace App\Controllers;
use Clases\Usuario;
use Clases\Empleados;
use Clases\Clientes;
use Clases\Mesas;
use Clases\Pedidos;


use App\Models\Empleado;
use App\Models\Pedido;

use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\EstadoPedido;
use App\Models\EstadoMesa;
use App\Models\Item;

use Clases\Token;
use Clases\Archivos;

class PedidoController {
    public $datos = array ('datos' => '.');
    
    public function alta($request, $response, $args)
    {
        $cantidad = 0;
        $parsedBody = $request->getParsedBody();

        // $bebida = $parsedBody['bebida'];
        // $comida = $parsedBody['comida'];
        // $bebida2 = $parsedBody['bebida2'];
        

        $token = $request->getHeader('token');

        $clienteAutenticado = Token::VerificarToken($token[0]);

        $cantidadDeItems = count($parsedBody);

        

        // var_dump($clienteAutenticado);

        //TODO: traerme cliente de la base y sacar el id, traerme los mozos y la disponibilidad, si alguno esta disponible sacar el id y cambiarle el estado a no disponible
        //dar de alta los items a pedir, dar de alta estado del pedido y asignar el id de la tabla estado_pedido a id_estado de la tabla pedidos

        if($clienteAutenticado){
            $clienteBase = Cliente::where('usuario', $clienteAutenticado->usuario)->where('clave',$clienteAutenticado->clave)->first();
        }

        $mozoDisponible = $this->obtenerMozoDisponible();

        
        
        // for ($i=0; $i < count($bebidaArray) ; $i++) {
            
        //     $cantidad = $i+1;
        // }
        
        //obtener tabla mesa, verificar disponibilidad, si esta disponible obtener el id
        
        $mesaDisponible = Mesa::where('id_estado',4)->first();

        $detalle = "";
        if($clienteBase != null){
            if( $mesaDisponible != null){
                if($mozoDisponible != null){
                    for ($i=0; $i < count($parsedBody); $i++) {
                        $j = $i+1;
                        $detalle .= $parsedBody['item'.$j] . " ";
                    }
                    $pedidoCreado = new Pedidos($detalle,count($parsedBody));

                    // $mesaNueva = $this->altaMesa($detalle,1);

                    $mozoDisponible->disponible = 0;
                    $mozoDisponible->operaciones = $mozoDisponible->operaciones+1;
                    $mozoDisponible->save();

                    $pedido = new Pedido;
                    
                    $pedido->codigo = $pedidoCreado->codigo;

                    $pedido->id_cliente = $clienteBase->id;
                    $pedido->id_estado = 1;
                    $pedido->detalle = "";
                    $pedido->id_empleado = $mozoDisponible->id;
                    $pedido->precio = $pedidoCreado->precio;
                    $pedido->id_sector = $mozoDisponible->id_sector;
                    $pedido->id_mesa = $mesaDisponible->id;
                    $pedido->tiempo = '00:00:00';
                    $mesaDisponible->id_estado = 1;
                    $mesaDisponible->save();

                    $pedido->save();
                    for ($i=0; $i < count($parsedBody); $i++) { 
                        $j = $i+1;
                        if($j<=count($parsedBody)){
                            $item = new Item;
                            if($parsedBody['item'.$j] != ""){

                                $item->descripcion = $parsedBody['item'.$j];
                            }
                            else{
                                $item->descripcion = " ";
                            }
                            $item->id_pedido = $pedido->id;
                            $item->id_empleado = $mozoDisponible->id;
                            $item->tiempoEstimado = '00:00:00';
                            
                            $item->save();
                        }
                    }
                
                    $datos['datos'] = 'Se creo el pedido correctamente!' . ' - Codigo del pedido: ' . $pedidoCreado->codigo
                    . " - Codigo de la mesa: " . $mesaDisponible->codigo;
                }
                else{
                    $datos['datos'] = 'No hay mozo disponible para atender, aguarde un instante por favor, gracias.';
                }
            }
            else{
                $datos['datos'] = 'No hay mesa disponible, aguarde un instante por favor, gracias.';
            }
            
            
        }
        else{
            $rta = false;
            $datos['datos'] = "No se encuentra el cliente en la base.";
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function obtenerMozoDisponible(){
        $mozosBase = Empleado::get()->where('tipo','mozo')->toArray();
        
        // $mozosBaseObj = json_encode($mozosBase);

        foreach ($mozosBase as $key => $value) {
            if($value['tipo'] == "mozo" && $value['disponible'] == 1){
                $mozoDisponible = Empleado::find($value['id']);
            break;
            }
            else{
                $mozoDisponible = null;
            }
            
        }
        return $mozoDisponible;
    }
    public function prepararPedido($request, $response, $args){

        /**El empleado que tomar ese pedido para prepararlo, al momento de hacerlo debe cambiar el
        estado de ese pedido como “en preparación” y agregarle un tiempo estimado de preparación.
        teniendo en cuenta que puede haber más de un empleado en el mismo puesto .ej: dos bartender
        o tres cocineros. */
        $parsedBody = $request->getParsedBody();

        $codigo = $parsedBody['codigo'];
        //si es cerveza ver disponibilidad de cervecero
        //si es comida empanada, pizza o hamburguesa ver disponibilidad de cocinero
        //si es tragos ver disponibilidad de bartender
        $pedidoBase = Pedido::where('codigo', $codigo)->first();
        
        $cerveceroDisponible = Empleado::where('tipo','cervecero')->where('disponible',1)->first();
        $cocineroDisponible = Empleado::where('tipo','cocinero')->where('disponible',1)->first();
        $bartenderDisponible = Empleado::where('tipo','bartender')->where('disponible',1)->first();

        if($pedidoBase != null){
            $itemsDePedido = Item::get()->where('id_pedido',$pedidoBase->id)->toArray();
            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'cerveza'){
                    //obtener bartender disponible y cambiar disponibilidad a 0
                    
                    //calcular tiempo estimado
                    if($cerveceroDisponible != null && $cerveceroDisponible->id_estado == 1){
                        $cerveceroDisponible->disponible = 0;
                        $cerveceroDisponible->operaciones = $cerveceroDisponible->operaciones+1;
                        $cerveceroDisponible->save();
    
                        
                        $itemActual->tiempoEstimado = '00:10:00';
                        $itemActual->id_empleado = $cerveceroDisponible->id;
                        $itemActual->save();
                        $aux1 = $itemActual->tiempoEstimado;
                        $pedidoBase->tiempo = $itemActual->tiempoEstimado;
                        // $pedidoBase->id_empleado = $cerveceroDisponible->id;
                        $pedidoBase->id_sector = 2;
                        
                        $datos['datos'] = 'El pedido de cerveza esta en preparacion';
                    break;
                    }
                    else{
                        $datos['datos'] = 'No hay disponibilidad de cervecero, aguarde un instante por favor, gracias.';
                    }
                }
                
            }
            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'empanadas'){
                    
                    if($cocineroDisponible != null){
                        $cocineroDisponible->disponible = 0;
                        $cocineroDisponible->operaciones = $cocineroDisponible->operaciones+1;
                        $cocineroDisponible->save();
                        $itemActual->id_empleado = $cocineroDisponible->id;
                        $itemActual->tiempoEstimado = '00:40:00';
                        $itemActual->save();
                        $aux2 = $itemActual->tiempoEstimado;
                        $pedidoBase->tiempo = $itemActual->tiempoEstimado;
                        $pedidoBase->id_empleado = $cocineroDisponible->id;
                        $pedidoBase->id_sector = 3;
                        
                        $datos['datos'] .= ' - El pedido de empanadas esta en preparacion.';
                    break;
                    }
                    else{
                        $datos['datos'] = 'No hay cocinero disponible, aguarde un instante, gracias.';
                    }
                }
            }
            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'vino'){
                    
                    if($bartenderDisponible != null){
                        $bartenderDisponible->disponible = 0;
                        $bartenderDisponible->operaciones = $bartenderDisponible->operaciones+1;
                        $bartenderDisponible->save();
                        $itemActual->id_empleado = $bartenderDisponible->id;
                        $itemActual->tiempoEstimado = '00:05:00';
                        $aux3 = $itemActual->tiempoEstimado;
                        $itemActual->save();

                        $pedidoBase->tiempo = $itemActual->tiempoEstimado;
                        $pedidoBase->id_empleado = $bartenderDisponible->id;
                        $pedidoBase->id_sector = 1;
                        
                        $datos['datos'] .= ' - El vino esta en preparacion.';
                    break;
                    }
                    else{
                        $datos['datos'] = 'No hay bartender disponible, aguarde un instante, gracias.';
                    }
                }
            }
           
            if($bartenderDisponible != null && $cocineroDisponible != null && $cerveceroDisponible != null){
                $tiempo1 = explode(':',$aux1);
                $tiempo2 = explode(':',$aux2);
                $tiempo3 = explode(':',$aux3);


                //comparo por minutos

                if($tiempo1[1] > $tiempo2[1]){
                    if($tiempo1[1] > $tiempo3[1]){
                        $pedidoBase->tiempo = $aux1;
                    }
                }
                else if($tiempo2[1] > $tiempo3[1]){
                    $pedidoBase->tiempo = $aux2;
                }
                else if($tiempo3[1] > $tiempo1[1]){
                    $pedidoBase->tiempo = $aux3;
                }
            }
            else{
                $datos['datos'] = 'Los empleados estan ocupados en este momento';
            }

            $pedidoBase->id_estado = 2;
            $pedidoBase->save();
            
        }
        else{
            $datos['datos'] = 'No se encuentra el pedido';
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function servirPedido($request, $response, $args){

        $parsedBody = $request->getParsedBody();

        $codigo = $parsedBody['codigo'];
        //si es cerveza ver disponibilidad de cervecero
        //si es comida empanada, pizza o hamburguesa ver disponibilidad de cocinero
        //si es tragos ver disponibilidad de bartender
        // var_dump($codigo);
        $pedidoBase = Pedido::where('codigo', $codigo)->first();
        // var_dump($pedidoBase);
        
        

        if($pedidoBase != null){
            $itemsDePedido = Item::get()->where('id_pedido',$pedidoBase->id)->toArray();
            $estadoDePedido = EstadoPedido::where('id',$pedidoBase->id_estado)->first();
            $mesa = Mesa::where('id',$pedidoBase->id_mesa)->first();

            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'cerveza'){
                    //obtener bartender disponible y cambiar disponibilidad a 0
                    $cervecero = Empleado::get()->where('id',$itemActual->id_empleado)->first();
                    
                    //calcular tiempo estimado
                    if($cervecero != null){
                        $cervecero->disponible = 1;
                        $cervecero->save();
                        // if($estadoDePedido->id == $)

                        $itemActual->tiempoEstimado = '00:00:00';
                        $itemActual->save();
                        
                        $datos['datos'] = 'El pedido de cerveza esta listo';
                    break;
                    }
                    else{
                        $datos['datos'] = 'No hay disponibilidad de cervecero, aguarde un instante por favor, gracias.';
                    }
                }
                
            }
            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'empanadas'){
                    $cocinero = Empleado::get()->where('id',$itemActual->id_empleado)->first();
                    if($cocinero != null){
                        $cocinero->disponible = 1;
                        $cocinero->save();
                        $itemActual->tiempoEstimado = '00:00:00';
                        $itemActual->save();
                        $pedidoBase->tiempo = $itemActual->tiempoEstimado;
                        $pedidoBase->save();
                        $datos['datos'] .= ' - El pedido de empanadas esta listo.';
                    break;
                    }
                    else{
                        $datos['datos'] = 'No hay cocinero disponible, aguarde un instante, gracias.';
                    }
                }
            }
            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'vino'){
                    $bartender = Empleado::get()->where('id',$itemActual->id_empleado)->first();
                    if($bartender != null){
                        $bartender->disponible = 1;
                        $bartender->save();
                        $itemActual->tiempoEstimado = '00:00:00';
                        $itemActual->save();
                        $datos['datos'] .= ' - El vino esta listo.';
                    break;
                    }
                    else{
                        $datos['datos'] = 'No hay bartender disponible, aguarde un instante, gracias.';
                    }
                }
            }
            if($mesa != null){
                $mesa->id_estado = 2;
                $mesa->save();
            }
            
            $pedidoBase->id_estado = 3;
            $pedidoBase->save();
            
        }
        else{
            $datos['datos'] = 'No se encuentra el pedido';
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function getPedido($request, $response, $args){

        $codigoMesa = $args['codigoMesa'];
        $codigoPedido = $args['codigoPedido'];
        
        $mesaBase = Mesa::where('codigo', $codigoMesa)->first();
        $pedidoBase = Pedido::where('codigo', $codigoPedido)->first();
        
        if($pedidoBase != null && $mesaBase!= null){
            if($pedidoBase->id_mesa == $mesaBase->id){
                $estadoDePedido = EstadoPedido::where('id',$pedidoBase->id_estado)->first();
                $datos['datos'] = "Estado del pedido: " . $estadoDePedido->descripcion . " " . " - Tiempo estimado de entrega: " . $pedidoBase->tiempo;
            }
            else{
                $datos['datos'] = 'Algunos de los codigos son incorrectos';
            }
        }
        else{
            $datos['datos'] = 'Algunos de los codigos son incorrectos';
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
          

    }
    public function cobrarMesa($request, $response, $arg){
        $parsedBody = $request->getParsedBody();
        $nombreMozo = $parsedBody['nombre'];
        $apellidoMozo = $parsedBody['apellido'];
        $codigoMesa = $parsedBody['codigoMesa'];
        if($nombreMozo != null && $nombreMozo != "" && $apellidoMozo != null && $apellidoMozo != ""){
            
            if($codigoMesa != null){

                
                $mozoBase = Empleado::where('nombre',$nombreMozo)->where('apellido',$apellidoMozo)->first();
                $mesaBase = Mesa::where('codigo',$codigoMesa)->first();
                if($mozoBase != null){
                    
                    if($mozoBase->disponible == 1){
                        if($mesaBase->id_estado == 2){
                            $mesaBase->id_estado = 3;
                            $mesaBase->save();
                            $pedido = Pedido::where('id_mesa',$mesaBase->id)->first();
                            // var_dump($pedido);
                            $datos['datos'] = 'La cantidad a cobrar es de: $' . $pedido->precio;
                        }
                        else{
                            $datos['datos'] = 'La mesa ya se cobro.';
                        }
                    }
                    else{
                        $datos['datos'] = 'No hay mozo disponible para cobrar, aguarde un instante por favor, gracias.';
                    }
                }
                else{
                    $datos['datos'] = 'No se encuentra el nombre y apellido del mozo.';
                }
            }
            else{
                $datos['datos'] = 'Codigo incorrecto';
            }
        }
        else{
            $datos['datos'] = 'Codigo incorrecto';
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function cerrarMesa($request, $response, $args){
        $parsedBody = $request->getParsedBody();
        $codigoMesa = $parsedBody['codigoMesa'];
        if($codigoMesa!= ""){
            $mesa = Mesa::where('codigo',$codigoMesa)->first();
            // echo $mesa->id_estado;
            if($mesa!= null){
                if($mesa->id_estado == 3){
                    $mesa->id_estado = 4;
                    $mesa->save();
                    $datos['datos'] = 'Mesa cerrada correctamente';

                }
                else if($mesa->id_estado == 4){
                    $datos['datos'] = 'La mesa ya se encuentra cerrada.';
                }
                else{
                    $datos['datos'] = 'Clientes comiendo';
                }

            }
            else{
                $datos['datos'] = 'No se encuentra la mesa';
            }
        }
        else{
            $datos['datos'] = 'Codigo incorrecto';
        }
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function altaMesa($request, $response, $arg){
        $parsedBody = $request->getParsedBody();
        $descripcion = $parsedBody['descripcion'];
        if($descripcion != null && $descripcion != ""){
            $mesaNueva = new Mesas($descripcion);
            $mesaBase = new Mesa;
            $mesaBase->codigo = $mesaNueva->codigo;
            $mesaBase->descripcion = $mesaNueva->descripcion;
            // echo $mesaNueva->descripcion;
            $mesaBase->id_estado = 4;
            $mesaBase->save();
            $datos['datos'] = 'Mesa creada correctamente';
        }
        else{
            $mesaNueva = new Mesas("mesa para 4 personas");
            $mesaBase = new Mesa;
            $mesaBase->codigo = $mesaNueva->codigo;
            $mesaBase->descripcion = $mesaNueva->descripcion;
            $mesaBase->id_estado = 4;
            $mesaBase->save();
            $datos['datos'] = 'Mesa creada correctamente';
        }
        
        
        $payload = json_encode($datos);
        $response->getBody()->write($payload);
        
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function getPedidoSocio($request, $response, $args){

        // var_dump($args);
        
        //obtendo id de la mesa, id del pedido y verifico si el pedido->id_mesa es igual a mesa->id
        $pedidos = Pedido::get()->toArray();
        $datos['datos'] ="";
        
        if($pedidos != null){
            foreach ($pedidos as $key => $value) {
                $estadoDePedido = EstadoPedido::where('id',$value['id_estado'])->first();
                $cliente = Cliente::where('id',$value['id_cliente'])->first();
                $empleado = Empleado::where('id',$value['id_empleado'])->first();
                $mesa = Mesa::where('id',$value['id_mesa'])->first();
                $estadoMesa = EstadoMesa::where('id',$mesa->id_estado)->first();
                $items = Item::get()->where('id_pedido',$value['id'])->toArray();
                // var_dump($items);
                
                $datos['datos'] .= "<br> Estado del pedido: " . $estadoDePedido->descripcion 
                . "<br> -Codigo de pedido:" . "  " . $value['codigo']
                . "<br> -Tiempo estimado de entrega:" . "  " . $value['tiempo']
                . "<br> - Precio: " . $value['precio']
                . "<br> - Cliente: " . $cliente->nombre . ", " . $cliente->apellido
                . "<br> - Empleado: " . $empleado->nombre . ", " . $empleado->apellido
                . "<br> - Mesa: " . " Descripcion: " . $estadoMesa->descripcion . " - Codigo: " . $mesa->codigo;

                foreach ($items as $key => $value) {
                    $datos['datos'] .= "<br> - Items: " . $value['descripcion'];
                }
                $datos['datos']  .= "<br><br>";
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
    
    // $respuesta = new stdClass;
    // $respuesta->success = true;

    // $respuesta->data = $datos; 

    
}