<?php
declare(strict_types=1);

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\DisabledException;

class AccountDisabledException extends DisabledException
{
}
