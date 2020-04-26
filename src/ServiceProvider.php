<?php declare(strict_types = 1);

namespace Quest;

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
    public function boot() : void
    {
        Builder::macro('orderByFuzzy', fn($fields) => OrderByFuzzy::make($this, $fields));
        Builder::macro('whereFuzzy', fn($field, $value) => WhereFuzzy::make($this, $field, $value));
    }

}