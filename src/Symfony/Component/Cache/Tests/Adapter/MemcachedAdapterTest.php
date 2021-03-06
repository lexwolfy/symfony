<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Adapter;

use Cache\IntegrationTests\CachePoolTest;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class MemcachedAdapterTest extends CachePoolTest
{
    private static $memcached;

    public function createCachePool()
    {
        if (defined('HHVM_VERSION')) {
            $this->skippedTests['testDeferredSaveWithoutCommit'] = 'Fails on HHVM';
        }

        return new MemcachedAdapter(self::$memcached, str_replace('\\', '.', __CLASS__));
    }

    public static function setupBeforeClass()
    {
        self::$memcached = new \Memcached();
        self::$memcached->addServer('127.0.0.1', 11211);
        
        if (@fsockopen('127.0.0.1', 11211) === false) {
            $e = error_get_last();
            self::markTestSkipped($e['message']);
        }
    }

    public static function tearDownAfterClass()
    {
        self::$memcached->flush();
    }
}