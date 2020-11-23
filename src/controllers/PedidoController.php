<?php
namespace App\Controllers;
use Clases\Usuario;
use Clases\Empleados;
use Clases\Clientes;
use Clases\Pedidos;
use Clases\Mesas;


use App\Models\Empleado;
use App\Models\Pedido;

use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\EstadoPedido;
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
                    $mozoDisponible->save();

                    $pedido = new Pedido;
                    
                    $pedido->codigo = $pedidoCreado->codigo;

                    $pedido->id_cliente = $clienteBase->id;
                    $pedido->id_estado = 1;
                    // $pedido->tiempo = $pedidoCreado->tiempo;
                    $pedido->id_empleado = $mozoDisponible->id;
                    $pedido->precio = $pedidoCreado->precio;

                    $pedido->id_mesa = $mesaDisponible->id;
                    $mesaDisponible->id_estado = 1;
                    $mesaDisponible->save();

                    $pedido->save();
                    for ($i=0; $i < count($parsedBody); $i++) { 
                        $j = $i+1;
                        if($j<=count($parsedBody)){
                            $item = new Item;
                            $item->descripcion = $parsedBody['item'.$j];
                            $item->id_pedido = $pedido->id;
                            $item->id_empleado = $mozoDisponible->id;
                            
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
            $datos['datos'] = "Complete los campos correctamente.";
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
        
        
        
        if($pedidoBase != null){
            $itemsDePedido = Item::get()->where('id_pedido',$pedidoBase->id)->toArray();
            foreach ($itemsDePedido as $key => $value) {
                $itemActual = Item::where('id', $value['id'])->first();
                if($value['descripcion'] == 'cerveza'){
                    //obtener bartender disponible y cambiar disponibilidad a 0
                    $cerveceroDisponible = Empleado::where('tipo','cervecero')->where('disponible',1)->first();
                    //calcular tiempo estimado
                    if($cerveceroDisponible != null){
                        $cerveceroDisponible->disponible = 0;
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
                    $cocineroDisponible = Empleado::where('tipo','cocinero')->where('disponible',1)->first();
                    if($cocineroDisponible != null){
                        $cocineroDisponible->disponible = 0;
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
                    $bartenderDisponible = Empleado::where('tipo','bartender')->where('disponible',1)->first();
                    if($bartenderDisponible != null){
                        $bartenderDisponible->disponible = 0;
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
        
        $pedidoBase = Pedido::where('codigo', $codigo)->first();
        
        

        if($pedidoBase != null){
            $itemsDePedido = Item::get()->where('id_pedido',$pedidoBase->id)->toArray();
            $estadoDePedido = EstadoPedido::where('id',$pedidoBase->id_estado)->first();

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

        // var_dump($args);
        
        $codigos = $request->getQueryParams('codigoPedido');
        $codigoMesa = $codigos['codigoMesa'];
        $codigoPedido = $codigos['codigoPedido'];
        
        //obtendo id de la mesa, id del pedido y verifico si el pedido->id_mesa es igual a mesa->id
        
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
    public function getPedidoSocio($request, $response, $args){

        // var_dump($args);
        
        //obtendo id de la mesa, id del pedido y verifico si el pedido->id_mesa es igual a mesa->id
    
        
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
    
    public function altaMesa($detalle,$estado_mesa){

        
        $mesaObjeto = new Mesas($detalle);

        $mesa = new Mesa;
        $mesa->codigo = $mesaObjeto->codigo;
        $mesa->descripcion = $detalle;
        $mesa->id_estado = $estado_mesa;
        // $mesa->save();
        $mesa->save();
        
        return $mesa;
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