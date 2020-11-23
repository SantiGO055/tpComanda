<?php
namespace Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
class Database{
    public function __construct(){
        $capsule = new Capsule;
        
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'tpcomanda',
            'username'  => 'root',
            'password'  => '',
            // 'host'      => 'sql200.tonohost.com',
            // 'database'  => 'ottos_27262641_comanda',
            // 'username'  => 'ottos_27262641',
            // 'password'  => 's147523698',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        
        // Set the event dispatcher used by Eloquent models... (optional)
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        
        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();
        
        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();

    }
}


?>