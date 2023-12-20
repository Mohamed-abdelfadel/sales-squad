<?php
namespace App\Exceptions;

use Exception;


class TeamNotFoundException extends Exception
{
    protected $message = 'Team not found.';
    protected $code = 404;
}
