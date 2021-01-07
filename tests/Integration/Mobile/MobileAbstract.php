<?php

namespace App\Tests\Integration\Mobile;

use App\Tests\Integration\IntegrationAbstract;

class MobileAbstract extends IntegrationAbstract
{
    protected function getBrowserHeight() : int
    {
        return 740;
    }

    protected function getBrowserWidth() : int
    {
        return 360;
    }
}
