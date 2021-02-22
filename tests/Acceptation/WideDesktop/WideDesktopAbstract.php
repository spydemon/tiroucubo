<?php

namespace App\Tests\Acceptation\WideDesktop;

use App\Tests\Acceptation\IntegrationAbstract;

abstract class WideDesktopAbstract extends IntegrationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 2560;
    }
}
