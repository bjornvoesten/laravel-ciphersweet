<?php

namespace Bjornvoesten\CipherSweet;

class Attribute implements Contracts\Attribute
{
    protected $searchable = [];

    protected $indexes = [];

    public function __construct()
    {
        //
    }

    public function searchable($value = true)
    {
        $this->searchable = $value;

        return $this;
    }

    public function index(string $name, callable $closure = null)
    {
        $index = new Index($name);

        if ($closure) {
            call_user_func($closure, $index);
        }

        $this->indexes[$name] = $index;

        return $this;
    }

    public function toArray()
    {
        return $this->indexes;
    }
}
