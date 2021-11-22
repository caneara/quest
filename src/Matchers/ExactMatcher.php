<?php declare(strict_types = 1);

namespace Quest\Matchers;

class ExactMatcher extends BaseMatcher
{
    /**
     * The operator to use for the WHERE clause.
     *
     **/
    protected string $operator = '=';
}
