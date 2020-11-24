<?php
namespace App\Middlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Slim\Psr7\Response;
use Clases\Token;

class ClienteMiddleware{
    
    public function __invoke($request, $handler){
        $parsedBody = $request->getParsedBody();
        $token = $request->getHeader('token');
        // var_dump($request);
        // $jwt = true; //Validar el token;
        //tomar del header el token, llamar a clase que valida el token y respondemos
        
        // var_dump($user->tipo);
        $response = new Response();
        // echo $user->tipo;
        if($token[0] != ""){
            $user = Token::VerificarToken($token[0]);
            if($user->tipo == "cliente" || $user->tipo == "socio"){
                
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();
                $resp = new Response();
                $resp->getBody()->write($existingContent);
                return $resp;
            }
            else{
                //podria lanzar una excepcion, manejarla de otro lado
                $rta = array("rta"=> "Error, usted no es cliente para ver esta pagina");
                $response->getBody()->write(json_encode($rta));
                return $response->withStatus(403); //puedo responder un status o lanzar excepcion
            }
        }
        else{
            $rta = array("rta"=> "Token incorrecto.");
            $response->getBody()->write(json_encode($rta));
            return $response->withStatus(403); //puedo responder un status o lanzar excepcion
        }

        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        

        return $response;
    }





}


?>