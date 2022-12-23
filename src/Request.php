<?php

namespace Nasirinezhad\JustRest;

class Request 
{

    private static $inctase = null;

    protected static $uri = [];
    protected static $data = [];
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
            throw new RException('Wrong URI!');
        }

        self::$uri = explode('/',$uri);

        self::$method = $_SERVER['REQUEST_METHOD'];
        
        if(!empty($_POST)) {
            self::$data = $_POST;
        }else {
            self::$data = (array) json_decode(file_get_contents('php://input'));
            if (empty(self::$data)) {
                parse_str(file_get_contents('php://input'), self::$data);
            }
        }
    }

    public function __get($name)
    {
        if(isset(self::$data[$name])) {
            return self::$data[$name];
        }
        if (isset(self::$argv[$name])) {
            return self::$argv[$name];
        }
        throw new RException($name.' not found!');
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
        throw new RException($k.' not found!');
    }
    public function header($name)
    {
        $name = 'HTTP_'.strtoupper(str_replace('/-| /', '_', $name));
        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        return null;
    }

    public static function getURI()
    {
        if(self::$inctase == NULL) {
            throw new RException('Request is not constructed!');
        }

        return self::$uri;
    }
    public static function getMethod()
    {
        if(self::$inctase == NULL) {
            throw new RException('Request is not constructed!');
        }

        return self::$method;
    }
    public static function getInctase()
    {
        if(self::$inctase == NULL) {
            throw new RException('Request is not constructed!');
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
