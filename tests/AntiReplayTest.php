<?php

namespace Tourze\Workerman\AntiReplayProtocol\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Tourze\Workerman\AntiReplayProtocol\AntiReplay;
use Tourze\Workerman\AntiReplayProtocol\AntiReplayContext;
use Tourze\Workerman\AntiReplayProtocol\Config;
use Tourze\Workerman\ConnectionContext\ContextContainer;
use Workerman\Connection\ConnectionInterface;

class AntiReplayTest extends TestCase
{
    private ConnectionInterface $connection;
    private AbstractAdapter $cache;
    private ItemInterface $cacheItem;
    private LoggerInterface $logger;
    private Config $config;

    protected function setUp(): void
    {
        // 创建模拟对象
        $this->connection = $this->createMock(ConnectionInterface::class);
        $this->cache = $this->createMock(AbstractAdapter::class);
        $this->cacheItem = $this->createMock(ItemInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // 设置配置
        $this->config = new Config();
        $this->config->setCache($this->cache);
        $this->config->setLogger($this->logger);
        $this->config->setCheckLength(32);
        $this->config->setTtl(3600);

        // 设置AntiReplay静态配置
        AntiReplay::setConfig($this->config);
    }

    /**
     * 测试获取和设置配置
     */
    public function test_setAndGetConfig_storesCorrectly(): void
    {
        $newConfig = new Config();
        AntiReplay::setConfig($newConfig);

        $this->assertSame($newConfig, AntiReplay::getConfig());
    }

    /**
     * 测试缓冲区长度不足时的行为
     */
    public function test_input_withShortBuffer_returnsCheckLength(): void
    {
        $buffer = str_repeat('a', 20); // 20字节，小于checkLength 32

        $result = AntiReplay::input($buffer, $this->connection);

        $this->assertEquals(32, $result);
    }

    /**
     * 测试首次收到某数据时的正常行为
     */
    public function test_input_withFirstTimeBuffer_returnsBufferLength(): void
    {
        $buffer = str_repeat('a', 100); // 100字节，大于checkLength
        $hash = 'anti-replay-' . hash('xxh3', substr($buffer, 0, 32));

        // 模拟缓存行为 - 首次访问时缓存中没有
        $this->cache->method('hasItem')
            ->with($hash)
            ->willReturn(false);

        $this->cacheItem->method('expiresAfter')
            ->with(3600)
            ->willReturn($this->cacheItem);

        $this->cache->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->cacheItem);
            });

        $result = AntiReplay::input($buffer, $this->connection);

        $this->assertEquals(strlen($buffer), $result);
    }

    /**
     * 测试重复收到相同数据时的拒绝行为
     */
    public function test_input_withReplayedBuffer_returnsZero(): void
    {
        $buffer = str_repeat('a', 100); // 100字节
        $hash = 'anti-replay-' . hash('xxh3', substr($buffer, 0, 32));

        // 模拟缓存行为 - 重放时缓存中已存在
        $this->cache->method('hasItem')
            ->with($hash)
            ->willReturn(true);

        // 模拟连接的远程地址
        $this->connection->method('getRemoteAddress')
            ->willReturn('127.0.0.1:8080');

        // 验证日志记录
        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('检测到重放攻击'));

        $result = AntiReplay::input($buffer, $this->connection);

        $this->assertEquals(0, $result);
    }

    /**
     * 测试已通过检查的连接的行为
     */
    public function test_input_withPassedConnection_returnsBufferLength(): void
    {
        $buffer = str_repeat('a', 100); // 100字节

        // 使用 ContextContainer 模拟已通过检查的连接
        $contextContainer = ContextContainer::getInstance();
        $context = new AntiReplayContext();
        $context->setPassAntiReplayCheck(true);
        $contextContainer->setContext($this->connection, $context);

        $result = AntiReplay::input($buffer, $this->connection);

        $this->assertEquals(strlen($buffer), $result);
    }

    /**
     * 测试解码方法
     */
    public function test_decode_returnsOriginalData(): void
    {
        $data = 'test data';

        $result = AntiReplay::decode($data, $this->connection);

        $this->assertEquals($data, $result);
    }

    /**
     * 测试编码方法
     */
    public function test_encode_returnsOriginalData(): void
    {
        $data = 'test data';

        $result = AntiReplay::encode($data, $this->connection);

        $this->assertEquals($data, $result);
    }

    /**
     * 测试空缓冲区的边界情况
     */
    public function test_input_withEmptyBuffer_returnsCheckLength(): void
    {
        $buffer = '';

        $result = AntiReplay::input($buffer, $this->connection);

        $this->assertEquals(32, $result);
    }

    /**
     * 测试超大缓冲区的处理
     */
    public function test_input_withLargeBuffer_returnsBufferLength(): void
    {
        $buffer = str_repeat('a', 10000); // 10000字节
        $hash = 'anti-replay-' . hash('xxh3', substr($buffer, 0, 32));

        // 模拟缓存行为
        $this->cache->method('hasItem')
            ->with($hash)
            ->willReturn(false);

        $this->cacheItem->method('expiresAfter')
            ->with(3600)
            ->willReturn($this->cacheItem);

        $this->cache->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->cacheItem);
            });

        $result = AntiReplay::input($buffer, $this->connection);

        $this->assertEquals(strlen($buffer), $result);
    }

    protected function tearDown(): void
    {
        // 清理可能的静态属性
        $config = new Config();
        $config->setCache($this->createMock(AbstractAdapter::class));
        $config->setLogger($this->createMock(LoggerInterface::class));
        AntiReplay::setConfig($config);
        
        // 清理 ContextContainer
        ContextContainer::resetInstance();
    }
}
