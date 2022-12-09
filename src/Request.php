<?php

namespace Nasirinezhad\JustRest;

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
