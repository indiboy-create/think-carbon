## ThinkPHP-PanGu - 盘古ThinkPHP开源索引!
面向 ThinkPHP 的开源工程索引：组件、框架、服务与发布工具，按能力组织，持续整理。
仓库地址：**<https://github.com/indiboy-create/ThinkPHP-PanGu/>**
在线站点：**<https://indiboy-create.github.io/ThinkPHP-PanGu/>**

# youvision/think-carbon

基于 [nesbot/carbon](https://github.com/briannesbitt/Carbon) 的 ThinkPHP 8 日期时间扩展，统一处理门面、助手函数、容器绑定、本地化语言与应用时区。

## 特性

- 直接复用 `nesbot/carbon`，完整保留 Carbon 的日期计算、格式化、比较与本地化能力
- 支持 Carbon 2.72+ 与 Carbon 3.x
- 通过 ThinkPHP 服务自动注册，提供 `Carbon` 门面与 `carbon()` 助手函数
- 支持容器注入，`app('carbon')` 与 `app(CarbonManager::class)` 返回同一实例
- 统一应用 `locale`、`fallback_locale` 与 `timezone` 配置
- 同时支持可变的 `Carbon\Carbon` 与不可变的 `Carbon\CarbonImmutable`
- 所有创建方法都遵循应用时区，包括时间戳创建

## 环境要求

- PHP >= 8.0
- ThinkPHP >= 8.0
- nesbot/carbon >= 2.72

## 安装

```bash
composer require youvision/think-carbon
```

服务通过 Composer 的 `extra.think.services` 声明。执行以下命令生成服务清单并完成自动注册：

```bash
php think service:discover
```

如果应用未启用服务自动发现，也可以在 `app/service.php` 中手动注册：

```php
return [
    \Youvision\ThinkCarbon\Service::class,
];
```

## 配置

将包内配置文件复制到应用的 `config/carbon.php` 后按需修改：

```
vendor/youvision/think-carbon/config/carbon.php
```

| 配置项 | 默认值 | 说明 |
| --- | --- | --- |
| `locale` | `zh_CN` | Carbon 本地化语言。支持 `en`、`zh_TW`、`zh_Hans` 等 Carbon 语言标识，也兼容 `zh-cn` 写法。留空表示不修改当前语言 |
| `fallback_locale` | `en` | 缺少对应翻译时的回退语言。留空表示不设置回退语言 |
| `timezone` | `null` | 本包创建实例统一使用的时区，例如 `Asia/Shanghai`。`null` 表示跟随 ThinkPHP 的 `app.default_timezone` |

未复制配置文件时，服务会自动合并包内默认配置，开箱即用。修改配置后如已开启配置缓存，请执行：

```bash
php think clear --config
```

## 使用

### 助手函数

```php
// 当前时间，返回 Carbon\Carbon
carbon();

// 解析字符串、时间戳或 DateTimeInterface
carbon('2024-01-01 08:00:00');
carbon(1704067200);

// 指定单次调用的时区，优先于配置
carbon('2024-01-01 08:00:00', 'UTC');

// 当前时间，返回 Carbon\Carbon
carbon_now();

// 返回不可变实例 Carbon\CarbonImmutable
carbon_immutable('2024-01-01');
```

### 门面

```php
use Youvision\ThinkCarbon\Facade\Carbon;

Carbon::now()->format('Y-m-d H:i:s');
Carbon::today()->startOfMonth();
Carbon::parse('2024-01-01 08:00:00')->addDays(7);
Carbon::now()->subDays(7)->diffForHumans();
Carbon::createFromTimestamp(1704067200);
Carbon::createFromFormat('Y/m/d H:i', '2024/01/01 08:00');

// 不可变实例
$date = Carbon::immutable('2024-01-01')->addMonth();
```

门面支持 `CarbonManager` 的所有创建方法，并通过 `__call` 代理 Carbon 的其他公开静态方法，例如 `setTestNow()`、`hasTestNow()`、`macro()`。

### 容器注入

```php
use Youvision\ThinkCarbon\CarbonManager;

class ArticleService
{
    public function __construct(protected CarbonManager $carbon)
    {
    }

    public function publishAt(): string
    {
        return $this->carbon->parse('2024-01-01 08:00:00')->format('Y-m-d H:i:s');
    }
}
```

也可以直接使用容器标识：

```php
app('carbon')->now();
app(CarbonManager::class)->today();
app(CarbonManager::class)->immutable('2024-01-01');
```

### 直接使用 Carbon

不由本包创建时，仍然可以直接使用 Carbon 原生 API：

```php
use Carbon\Carbon;

Carbon::parse('2024-01-01')->format('Y-m-d');
```

## 时区与本地化

### 时区

- `timezone` 为 `null` 时，本包会使用 PHP 默认时区。ThinkPHP 初始化时会根据 `app.default_timezone` 设置该值，因此默认行为与框架一致。
- 显式配置 `timezone` 后，门面、助手函数与容器创建的所有实例都使用该时区，但不会修改全局 PHP 时区。
- 调用时传入的 `$timezone` 优先级最高，只影响当前实例。
- 时间戳本身表示同一时刻，`createFromTimestamp()` 也会转换为应用时区后再格式化，便于直接输出本地时间。

```php
// config/carbon.php
'timezone' => 'Asia/Shanghai',

carbon()->getTimezone()->getName();                  // Asia/Shanghai
carbon('2024-01-01', 'UTC')->getTimezone()->getName(); // UTC
```

### 本地化

服务启动时会自动执行语言设置：

```php
carbon()->diffForHumans();       // 3 分钟前
carbon()->translatedFormat('F');  // 一月
```

如需在运行期重新应用配置中的语言：

```php
app('carbon')->applyLocale();
```

## 测试

```bash
composer test
```

## 许可证

[MIT](LICENSE)