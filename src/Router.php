<?php

namespace Nasirinezhad\JustRest;

    class Router
    {
        private static $map = [];

        private $middleware = null;

        public static function getAction()
        {
            $uri = Request::getURI();
            $method = Request::getMethod();

            $action = self::$map;
            $args = count($uri);

            foreach ($uri as $v) {
                if (!array_key_exists($v, $action)) {
                    break;
                }
                $action = $action[$v];
                $args--;
            }

            Request::$argc = $args;

            if (array_key_exists('Bind', $action)) {
                return $action['Bind'];
            }

            if (array_key_exists($method.'-'.$args, $action)) {
                return $action[$method.'-'.$args];
            }
            
            if (array_key_exists($method.'-0', $action)) {
                return $action[$method.'-0'];
            }
            
            throw new RException('Method Not Found!');

        }

        private static function newRoute($path, $action, $method)
        {
            $uri = explode('/', $path);
            $args = [];

            $route = &self::$map;

            foreach($uri as $v) {
                if (empty($v)) {
                    continue;
                }
                if($v[0] == '{'){
                    $args[] = substr($v,1, strlen($v)-2);
                }else {
                    self::mapAppend($route, $v);
                    $route = &$route[$v];
                }
            }
            if($method == 'Bind') {
                $n = 'Bind';
            }else {
                $n = $method.'-'.count($args);
            }
            if (array_key_exists($n, $route)){
                throw new RException('Route Already defined!');
            }
            $route[$n] = new Action($action, $method == 'Bind');
            $route[$n]->setArgs($args);

            return $route[$n];
        }

        private static function mapAppend(&$route, $key)
        {
            if (!array_key_exists($key, $route)) {
                $route[$key] = [];
            }
            // $route = $route[$key];
        }

        public static function __callStatic($name, $args)
        {
            if (count($args) != 2) {
                throw new RException('Unknow route method!');
            }

            switch (strtolower($name)) {
                case 'get':
                    self::newRoute($args[0], $args[1], 'GET');
                    break;
                case 'put':
                    self::newRoute($args[0], $args[1], 'PUT');
                    break;
                case 'post':
                    self::newRoute($args[0], $args[1], 'POST');
                    break;
                case 'delete':
                case 'del':
                    self::newRoute($args[0], $args[1], 'DELETE');
                    break;
                case 'option':
                    self::newRoute($args[0], $args[1], 'OPRION');
                    break;
                case 'bind':
                    self::newRoute($args[0], $args[1], 'Bind');
                    break;
            }
        }
        public function __call($name, $args)
        {
            if (count($args) != 2) {
                throw new RException('Unknow route method!');
            }
            $action = null;
            switch (strtolower($name)) {
                case 'get':
                    $action = self::newRoute($args[0], $args[1], 'GET');
                    break;
                case 'put':
                    $action = self::newRoute($args[0], $args[1], 'PUT');
                    break;
                case 'post':
                    $action = self::newRoute($args[0], $args[1], 'POST');
                    break;
                case 'delete':
                case 'del':
                    $action = self::newRoute($args[0], $args[1], 'DELETE');
                    break;
                case 'option':
                    $action = self::newRoute($args[0], $args[1], 'OPRION');
                    break;
                case 'bind':
                    $action = self::newRoute($args[0], $args[1], 'Bind');
                    break;
            }
            if ($action && $this->middleware) {
                $action->setMiddleware($this->middleware);
            }
        }

        public static function middleware($method)
        {
            $obj = new Router();
            $obj->middleware = $method;
            return $obj;
        }



        public function setPrefix($prefix)
        {
            Request::setPrefix($prefix);
        }
    }
     