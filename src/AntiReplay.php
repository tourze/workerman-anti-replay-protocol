<?php

namespace Tourze\Workerman\AntiReplayProtocol;

use Symfony\Contracts\Cache\ItemInterface;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\ProtocolInterface;

/**
 * 防重放协议
 * 对头字节的N个字节进行判断，如果发现有重复，则直接拒绝
 * 比较麻烦的是，这个可能会被利用来瘫痪代理。
 *
 * @see https://github.com/shadowsocks/shadowsocks-org/issues/184
 */
class AntiReplay implements ProtocolInterface
{
    private static Config $config;

    public static function getConfig(): Config
    {
        return self::$config;
    }

    public static function setConfig(Config $config): void
    {
        self::$config = $config;
    }

    public static function input($buffer, ConnectionInterface $connection): int
    {
        $length = strlen($buffer);
        if (isset($connection->_passAntiReplayCheck)) {
            return $length;
        }

        $checkLen = self::getConfig()->getCheckLength();
        if ($length < $checkLen) {
            return $checkLen;
        }
        //$hash = md5(substr($recv_buffer, 0, $checkLen));
        $hash = 'anti-replay-' . hash('xxh3', substr($buffer, 0, $checkLen));
        // 如果缓存已经存在，那么就说明发送过了
        if (self::getConfig()->getCache()->hasItem($hash)) {
            self::getConfig()->getLogger()->error('检测到重放攻击，来自' . $connection->getRemoteAddress());
            return 0;
        }

        // 默认24小时内不允许重放
        $checkTTL = self::getConfig()->getTtl();
        self::getConfig()->getCache()->get($hash, function (ItemInterface $item) use ($checkTTL) {
            $item->expiresAfter($checkTTL);
            return 1;
        });
        return $length;
    }

    public static function decode($buffer, ConnectionInterface $connection): string
    {
        return $buffer;
    }

    public static function encode($data, ConnectionInterface $connection): string
    {
        return $data;
    }
}
