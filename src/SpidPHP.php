<?php

namespace SpidPHP;
 
class SpidPHP
{
    private $protocol;

    public function __construct(SpInterface $protocol)
    {
        $this->protocol = $protocol;
    }

    public function __call($method, $arguments)
    {
        $methods_implemented = get_class_methods(array_shift($this->protocol));
        var_dump($methods_implemented);      die;  
        call_user_func_array(array($this->protocol, $method), $arguments);
    }
}
