<?php declare(strict_types = 1);

namespace Quest\Matchers;

class ConsecutiveCharactersMatcher extends BaseMatcher
{

    /**
     * The operator to use for the WHERE clause.
     *
     **/
    protected string $operator = 'LIKE';



    /**
     * The process for building the query string.
     *
     **/
    public function buildQueryString(string $field, string $value) : string
    {
        $search = $this->formatSearchString($value);

        return "IF(REPLACE($field, '\.', '') {$this->operator} '$search', ROUND({$this->multiplier} * " .
               "(CHAR_LENGTH('$value') / CHAR_LENGTH(REPLACE($field, ' ', '')))), 0)";
    }



    /**
     * Format the given search term.
     *
     **/
    public function formatSearchString(string $value) : string
    {
        $results = [];

        preg_match_all('/./u', $value, $results);

        return '%' . implode('%', $results[0]) . '%';
    }
}
