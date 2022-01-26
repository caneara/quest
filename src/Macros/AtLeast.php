<?php declare(strict_types = 1);

namespace Quest\Macros;

use Illuminate\Database\Query\Builder;

class AtLeast
{
    /**
     * Construct a fuzzy search expression.
     *
     **/
    public static function make(Builder $builder, int $minScore) : Builder
    {
        $builder->having('_fuzzy_relevance_', '>',  $minScore);
        return $builder;
    }
}
