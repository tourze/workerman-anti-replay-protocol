<?php

namespace Tourze\Workerman\AntiReplayProtocol;

/**
 * 防重放协议上下文
 * 存储连接相关的反重放检查状态
 */
class AntiReplayContext
{
    private bool $passAntiReplayCheck = false;

    public function isPassAntiReplayCheck(): bool
    {
        return $this->passAntiReplayCheck;
    }

    public function setPassAntiReplayCheck(bool $passAntiReplayCheck): void
    {
        $this->passAntiReplayCheck = $passAntiReplayCheck;
    }
}