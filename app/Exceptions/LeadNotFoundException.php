<?php
namespace App\Exceptions;

use Exception;


class LeadNotFoundException extends Exception
{
    protected $message = 'Lead not found.';
    protected $code = 404;
}
