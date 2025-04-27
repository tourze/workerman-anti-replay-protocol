<?php

namespace Tourze\Workerman\AntiReplayProtocol\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Tourze\Workerman\AntiReplayProtocol\Config;

class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config();
    }

    /**
     * 测试默认检查长度值
     */
    public function test_getCheckLength_returnsDefaultValue(): void
    {
        // 默认值应为32
        $this->assertEquals(32, $this->config->getCheckLength());
    }

    /**
     * 测试设置和获取检查长度
     */
    public function test_setAndGetCheckLength_changesValueCorrectly(): void
    {
        $this->config->setCheckLength(64);
        $this->assertEquals(64, $this->config->getCheckLength());
    }

    /**
     * 测试设置和获取缓存
     */
    public function test_setAndGetCache_storesCorrectly(): void
    {
        $mockCache = $this->createMock(AbstractAdapter::class);

        $this->config->setCache($mockCache);
        $this->assertSame($mockCache, $this->config->getCache());
    }

    /**
     * 测试默认TTL值
     */
    public function test_getTtl_returnsDefaultValue(): void
    {
        // 默认值应为24小时
        $this->assertEquals(60 * 60 * 24, $this->config->getTtl());
    }

    /**
     * 测试设置和获取TTL
     */
    public function test_setAndGetTtl_changesValueCorrectly(): void
    {
        $this->config->setTtl(3600);
        $this->assertEquals(3600, $this->config->getTtl());
    }

    /**
     * 测试设置和获取日志器
     */
    public function test_setAndGetLogger_storesCorrectly(): void
    {
        $mockLogger = $this->createMock(LoggerInterface::class);

        $this->config->setLogger($mockLogger);
        $this->assertSame($mockLogger, $this->config->getLogger());
    }

    /**
     * 测试checkLength为0的边界情况
     */
    public function test_setCheckLength_withZeroValue(): void
    {
        $this->config->setCheckLength(0);
        $this->assertEquals(0, $this->config->getCheckLength());
    }

    /**
     * 测试checkLength为负值的边界情况
     */
    public function test_setCheckLength_withNegativeValue(): void
    {
        $this->config->setCheckLength(-10);
        $this->assertEquals(-10, $this->config->getCheckLength());
    }

    /**
     * 测试TTL为0的边界情况
     */
    public function test_setTtl_withZeroValue(): void
    {
        $this->config->setTtl(0);
        $this->assertEquals(0, $this->config->getTtl());
    }

    /**
     * 测试TTL为负值的边界情况
     */
    public function test_setTtl_withNegativeValue(): void
    {
        $this->config->setTtl(-3600);
        $this->assertEquals(-3600, $this->config->getTtl());
    }
}
