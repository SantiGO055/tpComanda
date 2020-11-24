<?php
require __DIR__ . '/vendor/autoload.php';
// require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/database.php';

use Clases\Profesor;
use Clases\Materias;
use Clases\Token;
use Clases\Usuario;
use Clases\Materia;
use Config\Database;
use \Firebase\JWT\JWT;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Slim\Exception\HttpNotFoundException;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Middleware\ErrorMiddleware;
use Slim\Exception\NotFoundException;

use App\Controllers\SocioController;
use App\Controllers\EmpleadoController;
use App\Controllers\ClienteController;
use App\Controllers\PedidoController;

use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\UserMiddleware;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\ProfesorMiddleware;
use App\Middlewares\AlumnoMiddleware;
use App\Middlewares\AdminProfesorMiddleware;
use App\Middlewares\ClienteMiddleware;


session_start();
// $container = new Container();
// AppFactory::setContainer($container);
$app = AppFactory::create();

// $app->setBasePath("/tpComandaSantiagoGonzalez");

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();


new Database();


$app->group('', function (RouteCollectorProxy $group) {
    $group->post('/altaSocio', SocioController::class . ":registro");
    $group->post('/altaEmpleado', EmpleadoController::class . ":registro");
    $group->post('/altaCliente', ClienteController::class . ":registro");
    $group->post('/altaPedido', PedidoController::class . ":alta")->add(new ClienteMiddleware);
    $group->get('/getPedido/codigoMesa/{codigoMesa}/codigoPedido/{codigoPedido}', PedidoController::class . ":getPedido")->add(new ClienteMiddleware);
    $group->post('/prepararPedido', PedidoController::class . ":prepararPedido");
    $group->post('/servirPedido', PedidoController::class . ":servirPedido");
    $group->post('/cobrarMesa', PedidoController::class . ":cobrarMesa");

    $group->group('', function (RouteCollectorProxy $groupAdmin) {
        $groupAdmin->get('/getPedidoSocio', PedidoController::class . ":getPedidoSocio");
        $groupAdmin->get('/getEmpleados', SocioController::class . ":getEmpleados");
        $groupAdmin->get('/getAllPedidos', SocioController::class . ":getAllPedidos");
        $groupAdmin->post('/cerrarMesa', PedidoController::class . ":cerrarMesa");
        $groupAdmin->post('/altaMesa', PedidoController::class . ":altaMesa");


    })->add(new AdminMiddleware);
    // $group->post('/altaMesa', PedidoController::class . ":altaMesa")->add(new AdminMiddleware);

});
// ->add(new UserMiddleware)->add(new AuthMiddleware); //primero se ejecuta el ultimo, si no da ok el auth no se ejecuta el userMiddleware
$app->add(new JsonMiddleware);


$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->run();
?>