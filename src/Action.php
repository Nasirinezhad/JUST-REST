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
                $this->cname .= ':'.$act[1];
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
