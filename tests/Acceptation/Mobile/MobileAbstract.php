<?php

namespace App\Tests\Acceptation\Mobile;

use App\Tests\Acceptation\IntegrationAbstract;

class MobileAbstract extends IntegrationAbstract
{

    protected function getBrowserWidth() : int
    {
        return 360;
    }
}
