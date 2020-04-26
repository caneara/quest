<?php declare(strict_types = 1);

namespace Quest\Matchers;

class StartOfWordsMatcher extends BaseMatcher
{

    /**
     * The operator to use for the WHERE clause.
     *
     **/
    protected string $operator = 'LIKE';



    /**
     * Format the given search term.
     *
     **/
    public function formatSearchString(string $value) : string
    {
        return implode('% ', explode(' ', $value)) . '%';
    }

}