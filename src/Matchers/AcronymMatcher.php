<?php declare(strict_types = 1);

// Namespace
namespace Quest\Matchers;

// Acronym matcher
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