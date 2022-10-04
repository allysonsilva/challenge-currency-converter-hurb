<?php

namespace Support\Management;

use Illuminate\Cache\PhpRedisLock;
use Illuminate\Support\Traits\ForwardsCalls;
use Support\APIs\ExchangeRate\Redis\Repository as RedisRepository;

class RedisLock
{
    use ForwardsCalls;

    public readonly RedisRepository $cacheRepository;

    /**
     * Create a new lock instance.
     *
     * @return void
     */
    public function __construct(RedisRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * Get a lock instance.
     *
     * @param string $lockName
     * @param int $seconds
     *
     * @return \Illuminate\Cache\PhpRedisLock
     */
    public function lock(string $lockName, int $seconds = 0): PhpRedisLock
    {
        return new PhpRedisLock($this->cacheRepository->connection(), $lockName, $seconds);
    }

    /**
     * @param string $method
     * @param array<mixed> $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->forwardCallTo($this->cacheRepository, $method, $arguments);
    }
}
