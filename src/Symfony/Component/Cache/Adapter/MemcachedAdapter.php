<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Adapter;

class MemcachedAdapter extends AbstractAdapter
{
    private $memcached;

    public function __construct(\Memcached $memcached, $namespace = '', $defaultLifetime = 0)
    {
        $this->memcached = $memcached;

        parent::__construct($namespace, $defaultLifetime);
    }

    /**
     * @inheritDoc
     */
    protected function doFetch(array $ids)
    {
        return $this->memcached->getMulti($ids);
    }

    /**
     * @inheritDoc
     */
    protected function doHave($id)
    {
        return false !== $this->memcached->get($id)
        || $this->memcached->getResultCode() !== \Memcached::RES_NOTFOUND;
    }

    /**
     * @inheritDoc
     */
    protected function doClear($namespace)
    {
         return $this->memcached->flush();
    }

    /**
     * @inheritDoc
     */
    protected function doDelete(array $ids)
    {
        return $this->memcached->deleteMulti($ids)
            || $this->memcached->getResultCode() === \Memcached::RES_NOTFOUND;
    }

    /**
     * @inheritDoc
     */
    protected function doSave(array $values, $lifetime)
    {
        if ($lifetime > 30 * 24 * 3600) {
            $lifetime = time() + $lifetime;
        }

        return $this->memcached->setMulti($values, $lifetime);
    }

}