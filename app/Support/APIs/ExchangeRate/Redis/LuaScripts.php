<?php

namespace Support\APIs\ExchangeRate\Redis;

class LuaScripts
{
    public static function getRatesWithoutLock(): string
    {
        return <<<'LUA'
            -- When the lock key exists, then one must wait until the process that created
            -- the lock finishes so that the data in redis can be manipulated.
            if (redis.call('EXISTS', KEYS[1]) > 0) then
                return false
            end

            local args = ARGV

            if next(args) == nil then return {} end

            -- Gets multiple fields from a hash as a dictionary
            -- There is no lock, so rates can be returned
            local rates = redis.call("HMGET", KEYS[2], unpack(args))
            local result = {}

            for i, v in ipairs(rates) do
                result[args[i]] = v
            end

            return cjson.encode(result)
        LUA;
    }

    public static function updateExchangeRates(): string
    {
        return <<<'LUA'
            -- we decode the json string
            local rates = cjson.decode(ARGV[1])

            if next(rates) == nil then return nil end

            local bulk = {}

            for symbol, rate in pairs(rates) do
                table.insert(bulk, tostring(symbol))
                table.insert(bulk, tostring(rate))
            end

            -- Delete all the keys of the currently selected DB.
            -- redis.call('FLUSHDB', 'SYNC')

            redis.call('DEL', KEYS[1])

            -- hash set to redis according key and filed/value pair
            local numberFieldsAdded = redis.call('HSET', KEYS[1], unpack(bulk))

            return numberFieldsAdded
        LUA;
    }
}
