<?php declare(strict_types=1);

namespace Quest\Macros;

use Quest\Matchers\ExactMatcher;
use Illuminate\Support\Facades\DB;
use Quest\Matchers\AcronymMatcher;
use Quest\Matchers\InStringMatcher;
use Quest\Matchers\StudlyCaseMatcher;
use Illuminate\Database\Query\Builder;
use Quest\Matchers\StartOfWordsMatcher;
use Quest\Matchers\StartOfStringMatcher;
use Quest\Matchers\TimesInStringMatcher;
use Illuminate\Database\Query\Expression;
use Quest\Matchers\ConsecutiveCharactersMatcher;

class WhereFuzzy
{
    /**
     * The weights for the pattern matching classes.
     *
     **/
    protected static array $matchers = [
        ExactMatcher::class                 => 100,
        StartOfStringMatcher::class         => 50,
        AcronymMatcher::class               => 42,
        ConsecutiveCharactersMatcher::class => 40,
        StartOfWordsMatcher::class          => 35,
        StudlyCaseMatcher::class            => 32,
        InStringMatcher::class              => 30,
        TimesInStringMatcher::class         => 8,
    ];

    /**
     * Construct a fuzzy search expression.
     *
     **/
    public static function make(Builder $builder, $field, $value, $sortMatchesFilterRelevance, $disabledMatchers): Builder
    {
        $value       = static::escapeValue($value);
        $nativeField = '`' . str_replace('.', '`.`', trim($field, '` ')) . '`';

        if (! is_array($builder->columns) || empty($builder->columns)) {
            $builder->columns = ['*'];
        }

        $builder
            ->addSelect([static::pipeline($field, $nativeField, $value, $disabledMatchers)])
            ->when($sortMatchesFilterRelevance, function (Builder $query) use($field) {
                $query->having('fuzzy_relevance_' . str_replace('.', '_', $field), '>', 0);
            });

        static::calculateTotalRelevanceColumn($builder, $sortMatchesFilterRelevance);

        return $builder;
    }

    /**
     * Construct a fuzzy OR search expression.
     *
     **/
    public static function makeOr(Builder $builder, $field, $value, $relevance, $sortMatchesFilterRelevance, $disabledMatchers): Builder
    {
        $value       = static::escapeValue($value);
        $nativeField = '`' . str_replace('.', '`.`', trim($field, '` ')) . '`';

        if (! is_array($builder->columns) || empty($builder->columns)) {
            $builder->columns = ['*'];
        }

        $builder->addSelect([static::pipeline($field, $nativeField, $value, $disabledMatchers)])
            ->when($sortMatchesFilterRelevance, function (Builder $query) use($field, $relevance) {
                $query->orHaving('fuzzy_relevance_' . str_replace('.', '_', $field), '>', $relevance);
            });

        static::calculateTotalRelevanceColumn($builder, $sortMatchesFilterRelevance);

        return $builder;
    }

    /**
     * Manage relevance columns SUM for total relevance ORDER.
     *
     * Searches all relevance columns and parses the relevance
     * expressions to create the total relevance column
     * and creates the order statement for it.
     *
     */
    protected static function calculateTotalRelevanceColumn($builder, $sortMatchesFilterRelevance): bool
    {
        if (! empty($builder->columns)) {
            $existingRelevanceColumns = [];
            $sumColumnIdx             = null;

            // search for fuzzy_relevance_* columns and _fuzzy_relevance_ position
            foreach ($builder->columns as $as => $column) {
                if ($column instanceof Expression) {
                    if (stripos($column->getValue(DB::getQueryGrammar()), 'AS fuzzy_relevance_')) {
                        $matches = [];

                        preg_match('/AS (fuzzy_relevance_.*)$/', $column->getValue(DB::getQueryGrammar()), $matches);

                        if (! empty($matches[1])) {
                            $existingRelevanceColumns[$as] = $matches[1];
                        }
                    } elseif (stripos($column->getValue(DB::getQueryGrammar()), 'AS _fuzzy_relevance_')) {
                        $sumColumnIdx = $as;
                    }
                }
            }

            // glue together all relevance expresions under _fuzzy_relevance_ column
            $relevanceTotalColumn = '';

            foreach ($existingRelevanceColumns as $as => $column) {
                $relevanceTotalColumn .= (! empty($relevanceTotalColumn) ? ' + ' : '')
                    . '('
                    . str_ireplace(' AS ' . $column, '', $builder->columns[$as]->getValue(DB::getQueryGrammar()))
                    . ')';
            }

            $relevanceTotalColumn .= ' AS _fuzzy_relevance_';

            if (is_null($sumColumnIdx)) {
                // no sum column yet, just add this one
                $builder->addSelect([new Expression($relevanceTotalColumn)]);
            } else {
                // update the existing one
                $builder->columns[$sumColumnIdx] = new Expression($relevanceTotalColumn);
            }

            // only add the _fuzzy_relevance_ ORDER once
            if (
                ! $builder->orders
                || (
                    $builder->orders
                    && array_search(
                        '_fuzzy_relevance_',
                        array_column($builder->orders, 'column')
                    ) === false
                )
            ) {
                $builder->when($sortMatchesFilterRelevance, function (Builder $query) {
                    $query->orderBy('_fuzzy_relevance_', 'desc');;
                });
            }

            return true;
        }

        return false;
    }

    /**
     * Escape value input for fuzzy search.
     */
    protected static function escapeValue($value)
    {
        $value = str_replace(['"', "'", '`'], '', $value);
        $value = substr(DB::connection()->getPdo()->quote($value), 1, -1);

        return $value;
    }

    /**
     * Execute each of the pattern matching classes to generate the required SQL.
     *
     **/
    protected static function pipeline($field, $native, $value, $disabledMatchers): Expression
    {
        $sql = collect(static::$matchers)->forget($disabledMatchers)->map(
            fn ($multiplier, $matcher) => (new $matcher($multiplier))->buildQueryString("COALESCE($native, '')", $value)
        );

        return DB::raw($sql->implode(' + ') . ' AS fuzzy_relevance_' . str_replace('.', '_', $field));
    }
}
