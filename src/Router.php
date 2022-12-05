<?php

namespace Nasirinezhad\JustRest;

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
                throw new Exception('Wrong URI!');
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
                throw new Exception('Method Not Found!');
            }

            return $action['arg-'.$args];
        }

    }
     