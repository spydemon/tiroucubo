<?php

namespace App\Tests\Acceptation\WideDesktop;

use App\Tests\Acceptation\AcceptationAbstract;

abstract class WideDesktopAbstract extends AcceptationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 2560;
    }
}
