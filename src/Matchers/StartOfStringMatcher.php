<?php declare(strict_types = 1);

// Namespace
namespace Quest\Matchers;

// Start of string matcher
class StartOfStringMatcher extends BaseMatcher
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
        return "$value%";
    }

}