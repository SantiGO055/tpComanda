<?php
namespace App\Middlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Psr7\Response;
use Clases\Token;

class AdminMiddleware{

    
    public function __invoke($request, $handler){
        $parsedBody = $request->getParsedBody();
        $token = $request->getHeader('token');
        // $jwt = true; //Validar el token;
        //tomar del header el token, llamar a clase que valida el token y respondemos
        $response = new Response();
        
        // var_dump($token);
        
        if($token[0] != ""){
            $user = Token::VerificarToken($token[0]);
            
            if($user != null){
                if($user->tipo == "socio"){
                    
                    $response = $handler->handle($request);
                    $existingContent = (string) $response->getBody();
                    $resp = new Response();
                    $resp->getBody()->write($existingContent);
                    return $resp;
                }
                else{
                    //podria lanzar una excepcion, manejarla de otro lado
                    $rta = array("rta"=> "Error, usted no es administrador para ver esta pagina");
                    $response->getBody()->write(json_encode($rta));
                    return $response->withStatus(403); //puedo responder un status o lanzar excepcion
                }
            }
            // echo "token";
        }
        else{
            $rta = array("rta"=> "Token incorrecto.");
            $response->getBody()->write(json_encode($rta));
            return $response->withStatus(403); //puedo responder un status o lanzar excepcion
        }
        // var_dump($token);
        


        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        

        return $response;
    }





}


?>