# ZeroOne

ZeroOne 是一个基于 [Hyperf](https://hyperf.wiki/) 框架构建的高性能微服务/Web 应用骨架。它集成了常用的后台管理功能，旨在为快速开发提供坚实的基础。

## 🚀 技术栈

- **框架**: Hyperf 3.1
- **语言**: PHP >= 8.1
- **数据库**: MySQL
- **缓存**: Redis
- **搜索引擎**: Elasticsearch
- **其他**: Docker, Swoole

## 🛠 环境要求

在运行本项目之前，请确保您的环境满足以下要求：

- PHP >= 8.1
- Swoole PHP extension >= 5.0 (with `swoole.use_shortname` set to `Off` in `php.ini`)
- JSON PHP extension
- Pcntl PHP extension
- OpenSSL PHP extension
- PDO PHP extension
- Redis PHP extension
- GD PHP extension (用于验证码)

或者直接使用 Docker 环境。

## 📦 安装

### 1. 克隆项目

```bash
git clone <repository_url>
cd ZeroOne
```

### 2. 安装依赖

```bash
composer install
```

### 3. 配置环境

复制环境变量文件并修改配置：

```bash
cp .env.develop .env
# 编辑 .env 文件，配置数据库、Redis 等信息
vi .env
```

### 4. 数据初始化

如果需要初始化数据库：

```bash
# 运行数据库迁移 (如有)
php bin/hyperf.php migrate

# 运行数据填充
php bin/hyperf.php db:seed --class=InitAdmin
php bin/hyperf.php db:seed --class=InitRule
```

## 🏃‍♂️ 运行

### 本地运行

```bash
php bin/hyperf.php start
```
或者使用 `server:watch` 命令，自动编译代码并重启服务：
```bash
php bin/hyperf.php server:watch
```

服务默认运行在 `http://0.0.0.0:9501`。

### Docker 运行

```bash
docker-compose up -d
```

## 📂 目录结构

```text
ZeroOne/
├── app/
│   ├── Helper/          # 助手函数、枚举、Traits
│   ├── Http/
│   │   ├── Admin/       # 管理后台模块 (控制器、模型、逻辑)
│   │   └── Common/      # 公共模块
│   └── Exception/       # 异常处理
├── config/              # 配置文件
├── seeders/             # 数据库填充脚本
├── test/                # 测试用例
├── bin/                 # 启动脚本
└── .env                 # 环境变量
```

## ✨ 主要特性

- **模块化设计**: 分为 Admin 和 Common 模块，结构清晰。
- **权限管理**: 内置基于 RBAC 的权限管理系统 (AuthGroup, AuthRule)。
- **操作日志**: 自动记录后台操作日志 (AdminOpLog)。
- **验证码**: 集成图形验证码功能。
- **标准化响应**: 统一的 API 响应格式。
- **开发工具**: 集成 php-cs-fixer 代码格式化和 phpstan 静态分析。

## 🔧 开发命令

```bash
# 代码格式化
composer cs-fix

# 静态代码分析
composer analyse

# 运行测试
composer test
```

## ⚠️ 免责声明

1. 本项目仅供学习和研究使用，请勿用于非法用途。
2. 使用者在使用本项目时产生的一切后果由使用者自行承担，项目开发者不承担任何责任。
3. 本项目可能包含部分来源于网络的资源，版权归原作者所有。如有侵权，请联系删除。

## 📄 License

本项目遵循 [Apache-2.0](LICENSE) 开源协议。
