<?php

namespace App\Filters;
use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;
    protected $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        foreach($this->getFilters() as $filter => $value) {
            if(method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    public function getFilters()
    {
        // Take request data and intersect with filters
        // In other words we're checking if any filters are null
        return $this->request->intersect($this->filters);
    }
}
