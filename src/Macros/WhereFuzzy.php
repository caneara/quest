<?php declare(strict_types = 1);

// Namespace
namespace Quest\Macros;

// Using directives
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

// Matchers
use Quest\Matchers\ExactMatcher;
use Quest\Matchers\AcronymMatcher;
use Quest\Matchers\InStringMatcher;
use Quest\Matchers\StudlyCaseMatcher;
use Quest\Matchers\StartOfWordsMatcher;
use Quest\Matchers\StartOfStringMatcher;
use Quest\Matchers\TimesInStringMatcher;
use Quest\Matchers\ConsecutiveCharactersMatcher;

// Where fuzzy macro
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
	public static function make(Builder $builder, $field, $value) : Builder
	{
		$native = '`' . str_replace('.', '`.`', trim($field, '` ')) . '`';
		$value  = substr(DB::connection()->getPdo()->quote($value), 1, -1);

		if (! is_array($builder->columns) || empty($builder->columns)) {
			$builder->columns = ['*'];
		}

		return $builder
			 ->addSelect(static::pipeline($field, $native, $value))
			 ->orderBy('relevance_' . str_replace('.', '_', $field), 'desc')
			 ->having('relevance_' . str_replace('.', '_', $field), '>', 0);
	}



    /**
     * Execute each of the pattern matching classes to generate the required SQL.
     *
     **/
    protected static function pipeline($field, $native, $value) : Expression
    {
		$sql = collect(static::$matchers)->map(fn($multiplier, $matcher) =>
			(new $matcher($multiplier))->buildQueryString("COALESCE($native, '')", $value)
		);

		return DB::raw($sql->implode(' + ') . ' AS relevance_' . str_replace('.', '_', $field));
    }

}