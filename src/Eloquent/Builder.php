<?php

namespace Bjornvoesten\CipherSweet\Eloquent;

use Closure;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Add a basic where clause to the query.
     *
     * @param \Closure|string|array $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this|\Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (!$column instanceof Closure) {
            if (array_search($column, $this->model->getEncrypted()) !== false) {
                [$value, $operator] = $this->query->prepareValueAndOperator(
                    $value, $operator, func_num_args() === 2
                );

                return $this->whereEncrypted($column, $operator, $value, ['*'], $boolean);
            }
        }

        return parent::where(...func_get_args());
    }


    /**
     * Add an "or where" clause to the query.
     *
     * @param \Closure|array|string $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this|\Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function orWhere($column, $operator = null, $value = null, $boolean = 'or')
    {
        if (!$column instanceof Closure) {
            if (array_search($column, $this->model->getEncrypted()) !== false) {
                [$value, $operator] = $this->query->prepareValueAndOperator(
                    $value, $operator, func_num_args() === 2
                );


                return $this->orWhereEncrypted($column, $operator, $value, ['*'], $boolean);
            }
        }

        return parent::orWhere(...func_get_args());
    }

    /**
     * Add a where encrypted clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @param array $indexes
     * @param string $boolean
     * @return $this
     */
    public function whereEncrypted($column, $operator, $value = null, $indexes = ['*'], $boolean = 'and')
    {
        $query = new WhereEncrypted($this);

        [$value, $operator] = $this->query->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return app()->call(
            [$query, 'query'], [
                'column'   => $column,
                'operator' => $operator,
                'value'    => $value,
                'indexes'  => $indexes,
                'boolean'  => $boolean,
            ]
        );
    }

    /**
     * Add a or where encrypted clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @param array $indexes
     * @param string $boolean
     * @return $this
     */
    public function orWhereEncrypted($column, $operator, $value = null, $indexes = ['*'], $boolean = 'or')
    {
        [$value, $operator] = $this->query->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereEncrypted(
            $column, $operator, $value, $indexes, $boolean
        );
    }
}
