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

$app = AppFactory::create();
$app->setBasePath("/tpComandaSantiagoGonzalez");
// $app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();


new Database();


$app->group('', function (RouteCollectorProxy $group) {
    $group->post('/altaSocio', SocioController::class . ":registro");
    $group->post('/altaEmpleado', EmpleadoController::class . ":registro");
    $group->post('/altaCliente', ClienteController::class . ":registro");
    $group->post('/altaPedido', PedidoController::class . ":alta")->add(new ClienteMiddleware);
    $group->get('/getPedido', PedidoController::class . ":getPedido")->add(new ClienteMiddleware);
    $group->post('/prepararPedido', PedidoController::class . ":prepararPedido");
    $group->post('/servirPedido', PedidoController::class . ":servirPedido");
    $group->get('/getPedidoSocio', PedidoController::class . ":getPedidoSocio")->add(new AdminMiddleware);
    // $group->post('/altaMesa', PedidoController::class . ":altaMesa")->add(new AdminMiddleware);

    


    // $group->group('', function(RouteCollectorProxy $groupSocios) {
    //     $groupSocios->post('/materia', MateriaController::class . ":altaMateria")->add(new AdminMiddleware);
    //     $groupSocios->post('/inscripcion/{idMateria}', MateriaController::class . ":cargarInscripcion")->add(new AlumnoMiddleware);
    //     $groupSocios->put('/notas/{idMateria}', MateriaController::class . ":cargarNotas")->add(new ProfesorMiddleware);
    //     $groupSocios->get('/inscripcion/{idMateria}', MateriaController::class . ":MostrarInscriptos")->add(new AdminProfesorMiddleware);
    //     $groupSocios->get('/materia', MateriaController::class . ":MostrarMaterias");
    //     $groupSocios->get('/notas/{idMateria}', MateriaController::class . ":mostrarTodasLasNotas");

        
    // })->add(new UserMiddleware)->add(new AuthMiddleware);

});
// ->add(new UserMiddleware)->add(new AuthMiddleware); //primero se ejecuta el ultimo, si no da ok el auth no se ejecuta el userMiddleware
$app->add(new JsonMiddleware);


$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->run();
?>