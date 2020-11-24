<?php
namespace App\Middlewares;

use Slim\Psr7\Response;

class JsonMiddleware{


    public function __invoke($request,$handler){
        $response = $handler->handle($request);
        //como no hay new response corresponde al after
        $response = $response->withHeader('Content-Type','application/json');
        return $response;
    }
}


?>