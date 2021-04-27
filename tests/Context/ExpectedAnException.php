<?php
declare(strict_types=1);

namespace App\Tests\Context;

use RuntimeException;

class ExpectedAnException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Expect an exception');
    }
}
