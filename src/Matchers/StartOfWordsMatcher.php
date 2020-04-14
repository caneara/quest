<?php declare(strict_types = 1);

// Namespace
namespace Quest\Matchers;

// Start of words matcher
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