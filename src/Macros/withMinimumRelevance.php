<?php declare(strict_types = 1);

namespace Quest\Macros;

use Illuminate\Database\Query\Builder;

class withMinimumRelevance
{
    /**
     * Construct a fuzzy search expression.
     *
     */
    public static function make(Builder $builder, int $score) : Builder
    {
        return $builder->having('_fuzzy_relevance_', '>=',  $score);
    }
}
