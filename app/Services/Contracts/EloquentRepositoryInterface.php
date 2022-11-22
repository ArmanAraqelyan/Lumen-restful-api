<?php

namespace App\Services\Contracts;

interface EloquentRepositoryInterface
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function create (array $attributes);
}