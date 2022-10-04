<?php

namespace Support\APIs\ExchangeRate\Redis;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Support\APIs\ExchangeRate\Contracts\RedisConstants;
use Support\APIs\ExchangeRate\Exceptions\LuaScriptException;

class Repository
{
    use ForwardsCalls;

    /**
     * Redis instance used to handle the cache.
     */
    protected PhpRedisConnection $redis;

    public function __construct()
    {
        // @phpstan-ignore-next-line
        $this->redis = Redis::connection(RedisConstants::REDIS_CONNECTION);
    }

    /**
     * Configures a Redis database instance.
     *
     * @param \Illuminate\Redis\Connections\PhpRedisConnection $redis
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function setRedis(PhpRedisConnection $redis): void
    {
        $this->redis = $redis;
    }

    /**
     * Get the Redis database instance.
     *
     * @return \Illuminate\Redis\Connections\PhpRedisConnection
     *
     * @codeCoverageIgnore
     */
    public function connection(): PhpRedisConnection
    {
        return $this->redis;
    }

    /**
     * Forget the item from the cache.
     *
     * @param string $key
     *
     * @return $this
     */
    public function forget(string $key): static
    {
        $this->redis->del($key);

        return $this;
    }

    /**
     * Store a new item (HASH) in the cache.
     *
     * @param string $key
     * @param array<mixed> $value
     *
     * @return $this
     */
    public function storeHashInCache(string $key, array $value): static
    {
        $this->storeInCache($key, $value, 'hMSet');

        return $this;
    }

    /**
     * Returns the values associated with the specified fields in the hash stored at key.
     *
     * @param string $key
     * @param array<mixed> $memberKeys
     *
     * @return array<string, string>
     */
    public function getHashFromCache(string $key, array $memberKeys): array
    {
        return array_filter($this->redis->client()->hMGet($key, $memberKeys));
    }

    /**
     * Get an item from the cache if it exists.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getFromCache(string $key): mixed
    {
        return $this->redis->hGetAll($key) ?: ($this->redis->get($key) ?? null);
    }

    /**
     * Store a new item (STRING) in the cache.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function storeItemInCache(string $key, string $value): static
    {
        $this->storeInCache($key, $value, 'set');

        return $this;
    }

    /**
     * Determine whether if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function existsInCache(string $key): bool
    {
        return boolval($this->redis->exists($key));
    }

    /**
     * Evaluate a LUA script serverside.
     *
     * @param string $script
     * @param array<mixed> $keys
     * @param array<mixed> $args
     *
     * @return mixed
     *
     * @throws \Support\APIs\ExchangeRate\Exceptions\LuaScriptException
     */
    public function runLuaScript(string $script, array $keys = [], array $args = []): string|float|bool|null
    {
        $result = $this->redis->eval($script, count($keys), ...[...$keys, ...$args]);

        if (! empty($errorLUAScript = $this->getLastError())) {
            throw new LuaScriptException($errorLUAScript);
        }

        return $result;
    }

    /**
     * A string with the last returned script based error message, or NULL if there is no error.
     *
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->redis->getLastError();
    }

    /**
     * Store a new item in the cache.
     *
     * @param string $key
     * @param string|array<mixed> $value
     * @param string $method
     *
     * @return void
     */
    protected function storeInCache(string $key, string|array $value, string $method): void
    {
        $this->redis->{$method}($key, $value);
    }

    /**
     * @param string $method
     * @param array<mixed> $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->forwardCallTo($this->redis, $method, $arguments);
    }
}
