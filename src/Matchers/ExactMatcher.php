<?php declare(strict_types = 1);

// Namespace
namespace Quest\Matchers;

// Exact matcher
class ExactMatcher extends BaseMatcher
{

    /**
	 * The operator to use for the WHERE clause.
	 *
	 **/
    protected string $operator = '=';

}