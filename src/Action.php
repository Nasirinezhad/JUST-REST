<?php

namespace Nasirinezhad\JustRest;

class Action
{
    /**
     * 0: function
     * 1: function name
     * 2: Controller::Method
     * 3: Controller
     */
    private $type = 0; 

    //args
    private $args = [];
    
    public $argc = [];

    private $obj = NULL;

    private $cname = [];

    private $middleware = NULL;

    public function __construct($act, $bind) {
        if (is_callable($act)) {
            $this->obj = $act;
            $this->type = 0;
        }else if (is_string($act)) {
            $this->cname = explode(':',$act);
        }else if (is_array($act)) {
            $this->cname = $act;
        }

        if($bind){
            $this->type = 3;
        }else {
            $this->type = count($this->cname);
        }
    }
    public function setArgs($args)
    {
        $this->args = $args;
    }
    public function setMiddleware($obj)
    {
        if (is_string($this->middleware)) {
            $obj = explode(':', $obj);
        }
        $this->middleware = $obj;
    }
    private function middleware()
    {
        if($this->middleware == NULL) {
            return true;
        }

        if (is_callable($this->middleware)) {
            return ($this->middleware)(Request::getInctase());
        }
        
        if(count($this->middleware) == 2) {
            $obj = NULL;
            if(method_exists($this->middleware[0], 'getInstace')) {
                $obj = ($this->middleware[0])::getInstace();
            }else {
                $this->obj = new $this->middleware[0];
            }
            return call_user_method([$this->middleware[1], $obj], Request::getInctase());
        }

        if(count($this->middleware) == 1) {
            return call_user_func([$this->middleware[0]], Request::getInctase());
        }
    }

    public function call()
    {
        if(!$this->middleware()) {
            throw new Exception('middleware not accepted!');
        }

        if (!is_object($this->obj)) {
            $this->createOBJ();
        }

        switch ($this->type) {
            case 0:
            case 1:
                Request::getInctase()->nameArgs($this->args);
                return ($this->obj)(Request::getInctase());
            case 2:
                Request::getInctase()->nameArgs($this->args);
                return $this->obj->{$this->cname[1]}(Request::getInctase());
            case 3:
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
        throw new RException('Get method is not accepted');
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
        throw new RException('Post method is not accepted');
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
        throw new RException('Put method is not accepted');
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
        throw new RException('Delete method is not accepted');
    }
    private function OPTION()
    {
        $r = Request::getInctase();

        if(Request::$argc > 0 && method_exists($this->obj, 'option'.ucfirst($r->get(0)))) {
            $r->nameArgs(['method']);
            return $this->obj->{'option'.ucfirst($r->get(0))}($r);
        }
        throw new RException('method is not accepted');
    }

    private function createOBJ()
    {
        if (empty($this->cname)) {
            throw new RException('No method set!');
        }

        if($this->type == 1) {
            $this->obj = $this->cname[0];
        }else {
            if(!class_exists($this->cname[0])) {
                throw new RException('Class name '.$this->cname[0].' dose not exist!');
            }
            
            if(method_exists($this->cname[0], 'getInstace')) {
                $this->obj = ($this->cname[0])::getInstace();
            }
            
            if($this->obj == NULL){
                $this->obj = new $this->cname[0];
            }

            if($this->type == 2) {
                if(!method_exists($this->obj, $this->cname[1])) {
                    throw new RException('Method '.$this->cname[1]. ' not exist in class!');
                }
            }
        }
    }
}
