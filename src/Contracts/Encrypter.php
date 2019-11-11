<?php

namespace Bjornvoesten\CipherSweet\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Encrypter
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function encrypt(Model $model, string $column);

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function decrypt(Model $model, string $column);
}
