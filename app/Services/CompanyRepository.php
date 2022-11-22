<?php

namespace App\Services;

use App\Services\Contracts\CompanyRepositoryInterface;
use App\Models\Company;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    /**
     * @param Company $model
     */
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }
}