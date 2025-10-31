# Workerman 防重放协议

[![PHP Version Require](https://poser.pugx.org/tourze/workerman-anti-replay-protocol/require/php)](https://packagist.org/packages/tourze/workerman-anti-replay-protocol)
[![Latest Stable Version](https://poser.pugx.org/tourze/workerman-anti-replay-protocol/v/stable)](https://packagist.org/packages/tourze/workerman-anti-replay-protocol)
[![License](https://poser.pugx.org/tourze/workerman-anti-replay-protocol/license)](https://packagist.org/packages/tourze/workerman-anti-replay-protocol)
[![Total Downloads](https://poser.pugx.org/tourze/workerman-anti-replay-protocol/downloads)](https://packagist.org/packages/tourze/workerman-anti-replay-protocol)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/github/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

Workerman 防重放攻击协议实现，提供数据包重放检测和防护功能。

## 功能特点

- 防止重放攻击
- 可配置的检查长度
- 可配置的TTL (生存时间)
- 与Workerman框架集成
- 支持PSR日志

## 安装

```bash
composer require tourze/workerman-anti-replay-protocol
```

## 使用方法

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Log\LoggerInterface;
use Tourze\Workerman\AntiReplayProtocol\AntiReplay;
use Tourze\Workerman\AntiReplayProtocol\Config;

// 创建配置
$config = new Config();
$config->setCache(new FilesystemAdapter());
$config->setLogger($logger); // 实现了LoggerInterface的日志对象
$config->setCheckLength(32); // 可选，默认为32
$config->setTtl(60 * 60 * 24); // 可选，默认为24小时

// 设置防重放协议配置
AntiReplay::setConfig($config);

// 在Workerman中使用
$worker = new Worker('AntiReplay://0.0.0.0:8080');
$worker->onMessage = function($connection, $data) {
    // 处理数据
};
```

## 协议工作原理

防重放协议通过检查数据包的前N个字节来检测重放攻击。如果检测到相同的数据包头部，连接将被拒绝。

## 配置选项

- `checkLength`: 要检查的头字节长度，默认为32
- `cache`: 用于存储已处理请求哈希的缓存适配器
- `ttl`: 缓存的生存时间，默认为24小时
- `logger`: 用于记录重放攻击尝试的PSR兼容日志记录器

## 开发

### 安装依赖

```bash
composer install
```

### 运行测试

```bash
./vendor/bin/phpunit tests
```

## 参考文档

- [shadowsocks防重放攻击](https://github.com/shadowsocks/shadowsocks-org/issues/184)
- [Workerman文档](https://www.workerman.net/doc)

## License

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。
