<?php declare(strict_types=1);

namespace Quest;

use Closure;
use Quest\Macros\AtLeast;
use Quest\Macros\WhereFuzzy;
use Quest\Macros\OrderByFuzzy;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap any application services.
     *
     **/
    public function boot(): void
    {
        Builder::macro('orderByFuzzy', fn($fields) => OrderByFuzzy::make($this, $fields));

        Builder::macro('whereFuzzy', function($field, $value = null) {
            // check if first param is a closure and execute it if it is, passing the current builder as parameter
            // so when $query->orWhereFuzzy, $query will be the current query builder, not a new instance
            if ($field instanceof Closure) {
                $field($this);

                return $this;
            }

            // if $query->orWhereFuzzy is called in the closure, or directly by the query builder, do this
            return WhereFuzzy::make($this, $field, $value);
        });

        Builder::macro('orWhereFuzzy', function($field, $value = null) {
            if ($field instanceof Closure) {
                $field($this);

                return $this;
            }

            return WhereFuzzy::makeOr($this, $field, $value);
        });

        Builder::macro('atLeastFuzzy', fn($minScore) => AtLeast::make($this, $minScore));
    }
}
