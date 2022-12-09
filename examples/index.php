<?php

    /**
     * 1.include autolode to load classes
     */
    require_once '../vendor/autoload.php';

    use Nasirinezhad\JustRest\Server;

    /**
     * 2.include controllers
     */
    require_once './controlles/users.php';
    require_once './controlles/products.php';

    /**
     * 3.include defined routes
     */
    require_once './routes.php';


    /**
     * 4. lunch server
     */
    $app = new Server();
    $app->run();