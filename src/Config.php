<?php

namespace Tourze\Workerman\AntiReplayProtocol;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Tourze\Workerman\AntiReplayProtocol\Exception\InvalidConfigException;

class Config
{
    /**
     * @var int 要检查的头字节长度
     */
    private int $checkLength = 32;

    public function getCheckLength(): int
    {
        return $this->checkLength;
    }

    public function setCheckLength(int $checkLength): void
    {
        if ($checkLength <= 0) {
            throw new InvalidConfigException('checkLength must be greater than 0');
        }
        $this->checkLength = $checkLength;
    }

    /**
     * @var AbstractAdapter 缓存驱动
     */
    private AbstractAdapter $cache;

    public function getCache(): AbstractAdapter
    {
        return $this->cache;
    }

    public function setCache(AbstractAdapter $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @var int 过期TTL，默认24小时
     */
    private int $ttl = 60 * 60 * 24;

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): void
    {
        if ($ttl < 0) {
            throw new InvalidConfigException('ttl must be non-negative');
        }
        $this->ttl = $ttl;
    }

    private LoggerInterface $logger;

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
