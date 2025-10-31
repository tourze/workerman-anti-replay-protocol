<?php

namespace Tourze\Workerman\AntiReplayProtocol\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\Workerman\AntiReplayProtocol\Exception\InvalidConfigException;

/**
 * @internal
 */
#[CoversClass(InvalidConfigException::class)]
final class InvalidConfigExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionExtendsInvalidArgumentException(): void
    {
        $exception = new InvalidConfigException('test message');

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertEquals('test message', $exception->getMessage());
    }

    public function testExceptionWithoutMessage(): void
    {
        $exception = new InvalidConfigException();

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertEquals('', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previousException = new \RuntimeException('previous');
        $exception = new InvalidConfigException('test message', 123, $previousException);

        $this->assertEquals('test message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }
}
