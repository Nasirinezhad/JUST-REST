<?php

namespace Nasirinezhad\JustRest;

    class Router
    {
        private static $map = [];


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
            
            throw new \Exception('Method Not Found!');

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
                throw new \Exception('Route Already defined!');
            }
            $route[$n] = new Action($args, $action);

            return $route[$n];
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
            $route = self::newRoute($path, $action, 'DELETE');
        }
        public static function Option($path, $action)
        {
            $route = self::newRoute($path, $action, 'OPRION');
        }
        public static function Bind($path, $action)
        {
            $route = self::newRoute($path, $action, 'Bind');
        }

        public function setPrefix($prefix)
        {
            Request::setPrefix($prefix);
        }
    }
     