<?php declare(strict_types = 1);

// Namespace
namespace Quest\Tests;

// Using directives
use Illuminate\Database\Eloquent\Model;

// User
class User extends Model
{

	/**
	 * Disable timestamps.
	 *
	 **/
	public $timestamps = false;

}