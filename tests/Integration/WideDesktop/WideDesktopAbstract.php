<?php

namespace App\Tests\Integration\WideDesktop;

use App\Tests\Integration\IntegrationAbstract;

class WideDesktopAbstract extends IntegrationAbstract
{
    protected function getBrowserHeight() : int
    {
        return 1440;
    }

    protected function getBrowserWidth() : int
    {
        return 2560;
    }
}
