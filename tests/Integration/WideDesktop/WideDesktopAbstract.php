<?php

namespace App\Tests\Integration\WideDesktop;

use App\Tests\Integration\IntegrationAbstract;

abstract class WideDesktopAbstract extends IntegrationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 2560;
    }
}
