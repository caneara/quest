<?php declare(strict_types = 1);

namespace Quest\Matchers;

class AcronymMatcher extends BaseMatcher
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
        $results = [];

        preg_match_all('/./u', mb_strtoupper($value), $results);

        return implode('% ', $results[0]) . '%';
    }
}
