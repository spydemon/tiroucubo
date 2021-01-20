<?php

namespace App\Manager\Cache;

class NameManager
{
    /**
     * This function unsure us that the returned string only contains valid characters for being a cache keys.
     */
    public function encodeCacheKey(string $key) : string
    {
        return strtr($key, '{}()/\@:', '________');
    }
}
