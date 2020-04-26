<?php declare(strict_types = 1);

namespace Quest\Macros;

use Illuminate\Database\Query\Builder;

class OrderByFuzzy
{

    /**
     * Construct a fuzzy search expression.
     *
     **/
    public static function make(Builder $builder, $fields) : Builder
    {
        foreach ((array) $fields as $field) {
            $builder->orderBy('relevance_' . str_replace('.', '_', $field), 'desc');
        }

        return $builder;
    }

}