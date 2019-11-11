<?php

namespace Bjornvoesten\CipherSweet;

class Index implements Contracts\Index
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $bits = 256;

    /**
     * @var array
     */
    public $transformers = [];

    /**
     * @var bool
     */
    public $fast = true;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param callable $transformer
     * @return $this
     */
    public function transform(callable $transformer)
    {
        $this->transformers[] = $transformer;

        return $this;
    }

    /**
     * @param int $bits
     * @return $this
     */
    public function bits(int $bits)
    {
        $this->bits = $bits;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function fast(bool $value = true)
    {
        $this->fast = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function slow(bool $value = true)
    {
        $this->fast = !$value;

        return $this;
    }
}
