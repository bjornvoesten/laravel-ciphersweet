<?php

namespace Bjornvoesten\CipherSweet\Eloquent;

use Illuminate\Database\Eloquent\Builder;

class WhereEncrypted
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->app = app();
    }

    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param array $indexes
     * @param string $boolean
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function query($column, $operator, $value, $indexes = ['*'], $boolean = 'and')
    {
        $model = $this->builder->getModel();

        /** @var \Bjornvoesten\CipherSweet\Encrypter $encrypter */
        $encrypter = $this->app->make(
            'Bjornvoesten\CipherSweet\Contracts\Encrypter'
        );

        $columnIndexes = $encrypter->columnIndexes(
            $model, $column, $value
        );

        $indexes = collect($columnIndexes)
            ->filter(function ($value, $index) use ($indexes) {
                return array_search('*', $indexes) !== false
                    || array_search($index, $indexes) !== false;
            })
            ->all();

        $closure = function (Builder $query) use ($indexes, $column, $operator, $value) {
            foreach ($indexes as $index => $value) {
                $operator === '='
                    ? $query->orWhere($index, $operator, $value)
                    : $query->where($index, $operator, $value);
            }
        };

        $boolean === 'and'
            ? $this->builder->where($closure)
            : $this->builder->orWhere($closure);

        return $this->builder;
    }
}
