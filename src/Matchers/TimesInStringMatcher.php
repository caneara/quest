<?php declare(strict_types = 1);

// Namespace
namespace Quest\Matchers;

// Times in string matcher
class TimesInStringMatcher extends BaseMatcher
{

    /**
     * The process for building the query string.
	 *
     **/
    public function buildQueryString(string $field, string $value) : string
    {
        return "{$this->multiplier} * ROUND((CHAR_LENGTH($field) - CHAR_LENGTH(REPLACE(LOWER($field), " .
			   "LOWER('$value'), ''))) / LENGTH('$value'))";
    }

}