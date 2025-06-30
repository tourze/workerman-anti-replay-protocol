<?php

namespace Tourze\Workerman\AntiReplayProtocol\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\AntiReplayProtocol\AntiReplayContext;

class AntiReplayContextTest extends TestCase
{
    private AntiReplayContext $context;

    protected function setUp(): void
    {
        $this->context = new AntiReplayContext();
    }

    /**
     * 测试默认状态
     */
    public function test_defaultState_returnsFalse(): void
    {
        $this->assertFalse($this->context->isPassAntiReplayCheck());
    }

    /**
     * 测试设置为true
     */
    public function test_setPassAntiReplayCheck_withTrue_returnsTrue(): void
    {
        $this->context->setPassAntiReplayCheck(true);
        
        $this->assertTrue($this->context->isPassAntiReplayCheck());
    }

    /**
     * 测试设置为false
     */
    public function test_setPassAntiReplayCheck_withFalse_returnsFalse(): void
    {
        // 先设置为true
        $this->context->setPassAntiReplayCheck(true);
        $this->assertTrue($this->context->isPassAntiReplayCheck());
        
        // 再设置为false
        $this->context->setPassAntiReplayCheck(false);
        $this->assertFalse($this->context->isPassAntiReplayCheck());
    }
}