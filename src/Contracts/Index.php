<?php

namespace Bjornvoesten\CipherSweet\Contracts;

interface Index
{
    /**
     * @param callable $transformer
     * @return $this
     */
    public function transform(callable $transformer);

    /**
     * @param int $bits
     * @return $this
     */
    public function bits(int $bits);

    /**
     * @param bool $value
     * @return $this
     */
    public function fast(bool $value = true);

    /**
     * @param bool $value
     * @return $this
     */
    public function slow(bool $value = true);
}
