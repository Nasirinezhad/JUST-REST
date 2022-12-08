<?php

namespace Nasirinezhad\JustRest;

    class Action
    {
        /**
         * 0: function
         * 1: Controller::Method
         * 2: Controller #TODO
         */
        private $type = 0; 

        //args
        private $args = 0;

        private $obj = NULL;

        private $cname = '';

        public function __construct($args, $act) {
            $this->args = $args;
            if (is_object($act)) {
                $this->obj = $act;
                $this->type = 0;
            }else if (is_string($act)) {
                $this->cname = $act;
                $this->type = 1;
            }
        }

        public function call()
        {
            if (!is_object($this->obj)) {
                $this->createOBJ();
            }

            switch ($this->type) {
                case 0:
                    return ($this->obj)();
                case 1:
                    return $this->obj->{$this->cname}();
                case 2:
                    return $this->obj->{$this->cname}();
            }

            
        }

        private function createOBJ()
        {
            if ($this->cname == '') {
                throw new \Exception('No method set!');
            }
            
            $cname = explode(':', $this->cname);
            
            if(!class_exists($cname[0])) {
                throw new \Exception('Class name '.$cname[0].' dose not exist!');
            }
            
            
            if(method_exists($cname[0], 'getInstace')) {
                $this->obj = ($cname[0])::getInstace();
            }
            
            if($this->obj == NULL){
                $this->obj = new $cname[0];
            }

            if(!method_exists($this->obj, $cname[1])) {
                throw new \Exception('Method '.$cname[1]. ' not exist in class!');
            }

            $this->cname = $cname[1];
        }
    }
    

    class Router
    {
        private static $map = [
            'GET' => [],
            'POST' => [],
            'PUT' => [],
            'DEL' => [],
            'OPTION' => []
        ];
        private static $uri = [];
        private static $prefix = '/api';
        private $method;

        public function __construct()
        {
            
            if (substr($_SERVER['REQUEST_URI'], 0, strlen(self::$prefix)) == self::$prefix) {
                $uri = substr($_SERVER['REQUEST_URI'], strlen(self::$prefix));
            }else {
                throw new \Exception('Wrong URI!');
            }

            if($uri[0] == '/'){
                $uri = substr($uri, 1);
            }

            self::$uri = explode('/',$uri);

            $this->method = $_SERVER['REQUEST_METHOD'];;
        }

        public function getAction()
        {
            $action = self::$map[$this->method];
            $args = count(self::$uri);

            foreach (self::$uri as $v) {
                if (!array_key_exists($v, $action)) {
                    break;
                }
                $action = $action[$v];
                $args--;
            }

            if (!array_key_exists('arg-'.$args, $action)){
                throw new \Exception('Method Not Found!');
            }

            return $action['arg-'.$args];
        }

        private static function newRoute($path, $action, $method)
        {
            $uri = explode('/', $path);
            $args = [];

            $route = &self::$map[$method];

            foreach($uri as $v) {
                if($v[0] == '{'){
                    $args[] = substr($v,1, strlen($v)-2);
                }else {
                    self::mapAppend($route, $v);
                    $route = &$route[$v];
                }
            }
            $n = 'arg-'.count($args);
            if (array_key_exists($n, $route)){
                throw new \Exception('Route Already defined!');
            }
            $route[$n] = new Action($args, $action);
        }

        private static function mapAppend(&$route, $key)
        {
            if (!array_key_exists($key, $route)) {
                $route[$key] = [];
            }
            // $route = $route[$key];
        }

        public static function Get($path, $action)
        {
            $route = self::newRoute($path, $action, 'GET');
        }
        public static function Put($path, $action)
        {
            $route = self::newRoute($path, $action, 'PUT');
        }
        public static function Post($path, $action)
        {
            $route = self::newRoute($path, $action, 'POST');
        }
        public static function Del($path, $action)
        {
            $route = self::newRoute($path, $action, 'DEL');
        }
        public static function Option($path, $action)
        {
            $route = self::newRoute($path, $action, 'OPRION');
        }

        //test
        public static function test()
        {
            var_dump(self::$map);
        }
    }
     