<?php declare(strict_types = 1);

namespace Quest\Tests;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Disable timestamps.
     *
     **/
    public $timestamps = false;
}
