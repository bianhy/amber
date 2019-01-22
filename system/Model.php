<?php

namespace Amber\System;

use Illuminate\Database\Query\Builder;

abstract class Model
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $builder;

    public function getBuilder() {
        return $this->builder;
    }

    public function setBuilder(Builder $builder) {
        return $this->builder = $builder;
    }

    public function select($where, $columns= ['*'])
    {
        return $this->builder->select($columns)->where($where)->first();
    }

    public function selectAll($where, $columns= ['*'])
    {
        return  $this->builder->select($columns)->where($where)->get();
    }

    public function update($values, $where) {
        return $this->builder->where($where)->update($values);
    }

    public function insert($values) {
        return $this->builder->insert($values);
    }

    public function delete($where) {
        return $this->builder->where($where)->delete();
    }

    public function pages($where,$perPage = 15, $columns = ['*'], $total=0) {
        return $this->builder->where($where)->paginate($perPage, $columns, $total);
    }
}