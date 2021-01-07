<?php

namespace App\Tests\Integration\TabletLandscape;

use App\Tests\Integration\IntegrationAbstract;

class TabletLandscapeAbstract extends IntegrationAbstract
{
    protected function getBrowserHeight() : int
    {
        return 768;
    }

    protected function getBrowserWidth() : int
    {
        return 1024;
    }
}
