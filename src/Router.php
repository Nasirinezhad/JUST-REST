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
        private $args = [];
        
        public $argc = [];

        private $obj = NULL;

        private $cname = '';

        public function __construct($args, $act) {
            $this->args = $args;
            if (is_callable($act)) {
                $this->obj = $act;
                $this->type = 0;
            }else if (is_string($act)) {
                $this->cname = $act;
                $this->type = strpos(':', $act) > 0 ? 1 : 2;
            }else if (is_array($act)) {
                $this->cname = $act[0];
                if(count($act) == 1) {
                    $this->type = 2;
                }else {
                    $this->cname .= $act[1];
                    $this->type = 1;
                }
            }
        }

        public function call()
        {
            if (!is_object($this->obj)) {
                $this->createOBJ();
            }

            switch ($this->type) {
                case 0:
                    Request::getInctase()->nameArgs($this->args);
                    return ($this->obj)(Request::getInctase());
                case 1:
                    Request::getInctase()->nameArgs($this->args);
                    return $this->obj->{$this->cname}(Request::getInctase());
                case 2:
                    return $this->bind();
            }
        }

        private function bind()
        {
            return $this->{Request::getMethod()}();
        }

        private function GET()
        {
            $r = Request::getInctase();

            if(Request::$argc == 0 && method_exists($this->obj, 'index')) {
                return $this->obj->index($r);
            }
            if(Request::$argc == 1 && is_numeric($r->get(0)) && method_exists($this->obj, 'find')) {
                $r->nameArgs(['id']);
                return $this->obj->find($r);
            }
            if(Request::$argc > 0 && method_exists($this->obj, 'method'.ucfirst($r->get(0)))) {
                $r->nameArgs(['method']);
                return $this->obj->{'method'.ucfirst($r->get(0))}($r);
            }
            throw new \Exception('Get method is not accepted');
        }

        private function POST()
        {
            $r = Request::getInctase();

            if(Request::$argc == 0 && method_exists($this->obj, 'insert')) {
                return $this->obj->insert($r);
            }
            if(Request::$argc > 0 && method_exists($this->obj, 'method'.ucfirst($r->get(0)))) {
                $r->nameArgs(['method']);
                return $this->obj->{'method'.ucfirst($r->get(0))}($r);
            }
            throw new \Exception('Post method is not accepted');
        }

        private function PUT()
        {
            $r = Request::getInctase();

            if(Request::$argc == 0 && method_exists($this->obj, 'save')) {
                return $this->obj->save($r);
            }
            if(Request::$argc > 0 && method_exists($this->obj, 'method'.ucfirst($r->get(0)))) {
                $r->nameArgs(['method']);
                return $this->obj->{'method'.ucfirst($r->get(0))}($r);
            }
            throw new \Exception('Put method is not accepted');
        }

        private function DELETE()
        {
            $r = Request::getInctase();

            if(Request::$argc == 1 && method_exists($this->obj, 'remove')) {
                $r->nameArgs(['id']);
                return $this->obj->remove($r);
            }
            if(Request::$argc > 0 && method_exists($this->obj, 'method'.ucfirst($r->get(0)))) {
                $r->nameArgs(['method']);
                return $this->obj->{'method'.ucfirst($r->get(0))}($r);
            }
            throw new \Exception('Delete method is not accepted');
        }
        private function OPTION()
        {
            $r = Request::getInctase();

            if(Request::$argc > 0 && method_exists($this->obj, 'option'.ucfirst($r->get(0)))) {
                $r->nameArgs(['method']);
                return $this->obj->{'option'.ucfirst($r->get(0))}($r);
            }
            throw new \Exception('method is not accepted');
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

            if($this->type == 1) {

                if(!method_exists($this->obj, $cname[1])) {
                    throw new \Exception('Method '.$cname[1]. ' not exist in class!');
                }

                $this->cname = $cname[1];
            }
        }
    }
    
    class Request 
    {

        private static $inctase = null;

        protected static $uri = [];
        public static $argc = 0;
        protected static $argv = [];
        protected static $prefix;

        private static $method;


        public function __construct($prefix = '/api')
        {
            if(self::$inctase == null) {
                self::$inctase = $this;
                self::$prefix = $prefix;
            }

            if (substr($_SERVER['REQUEST_URI'], 0, strlen(self::$prefix)) == self::$prefix) {
                $uri = trim(substr($_SERVER['REQUEST_URI'], strlen(self::$prefix)), '/ ');
            }else {
                throw new \Exception('Wrong URI!');
            }

            self::$uri = explode('/',$uri);

            self::$method = $_SERVER['REQUEST_METHOD'];;
        }

        public function __get($name)
        {
            if((self::$method == 'POST' || self::$method == 'PUT') && isset($_POST[$name])) {
                return $_POST[$name];
            }
            if (isset(self::$argv[$name])) {
                return self::$argv[$name];
            }
            throw new \Exception($name.' not found!');
        }
        public function get($k)
        {
            $c = count(self::$uri) - self::$argc;
            if (is_numeric($k) && isset(self::$uri[$c+$k])) {
                return self::$uri[$c+$k];
            }
            if (isset(self::$argv[$k])) {
                return self::$argv[$k];
            }
            throw new Exception($k.' not found!');
        }

        public static function getURI()
        {
            if(self::$inctase == NULL) {
                throw new \Exception('Request is not constructed!');
            }

            return self::$uri;
        }
        public static function getMethod()
        {
            if(self::$inctase == NULL) {
                throw new \Exception('Request is not constructed!');
            }

            return self::$method;
        }
        public static function getInctase()
        {
            if(self::$inctase == NULL) {
                throw new \Exception('Request is not constructed!');
            }

            return self::$inctase;
        }
        public function setPrefix($prefix)
        {
            self::$prefix = $prefix;
        }

        public function nameArgs($names)
        {
            $c = count(self::$uri) - self::$argc;
            foreach ($names as $k => $v) {
                self::$argv[$v] = self::$uri[$c+$k];
            }
        }
    }

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
     