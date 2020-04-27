<?php declare(strict_types = 1);

namespace Quest\Matchers;

abstract class BaseMatcher
{

    /**
     * The weight to apply to the match.
     *
     **/
    protected int $multiplier;



    /**
     * Constructor.
     *
     **/
    public function __construct(int $multiplier)
    {
        $this->multiplier = $multiplier;
    }



    /**
     * The default process for building the query string.
     *
     **/
    public function buildQueryString(string $field, string $value) : string
    {
        if (method_exists($this, 'formatSearchString')) {
            $value = $this->formatSearchString($value);
        }

        return "IF($field {$this->operator} '$value', {$this->multiplier}, 0)";
    }
}
