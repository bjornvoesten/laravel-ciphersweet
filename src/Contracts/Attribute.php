<?php

namespace Bjornvoesten\CipherSweet\Contracts;

interface Attribute
{
    /**
     * @param string $name
     * @param callable|null $closure
     * @return $this
     */
    public function index(string $name, callable $closure = null);
}
