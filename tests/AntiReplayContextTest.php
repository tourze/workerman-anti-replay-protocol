<?php

namespace Tourze\Workerman\AntiReplayProtocol\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Workerman\AntiReplayProtocol\AntiReplayContext;

/**
 * @internal
 */
#[CoversClass(AntiReplayContext::class)]
final class AntiReplayContextTest extends TestCase
{
    private AntiReplayContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new AntiReplayContext();
    }

    /**
     * 测试默认状态
     */
    public function testDefaultStateReturnsFalse(): void
    {
        $this->assertFalse($this->context->isPassAntiReplayCheck());
    }

    /**
     * 测试设置为true
     */
    public function testSetPassAntiReplayCheckWithTrueReturnsTrue(): void
    {
        $this->context->setPassAntiReplayCheck(true);

        $this->assertTrue($this->context->isPassAntiReplayCheck());
    }

    /**
     * 测试设置为false
     */
    public function testSetPassAntiReplayCheckWithFalseReturnsFalse(): void
    {
        // 先设置为true
        $this->context->setPassAntiReplayCheck(true);
        $this->assertTrue($this->context->isPassAntiReplayCheck());

        // 再设置为false
        $this->context->setPassAntiReplayCheck(false);
        $this->assertFalse($this->context->isPassAntiReplayCheck());
    }
}
