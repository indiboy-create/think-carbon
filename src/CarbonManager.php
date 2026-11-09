<?php

declare(strict_types=1);

namespace Youvision\ThinkCarbon;

use BadMethodCallException;
use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use DateTimeZone;
use ReflectionMethod;

/**
 * Carbon 实例管理器，负责默认时区与本地化的统一处理。
 */
class CarbonManager
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $default = require dirname(__DIR__) . '/config/carbon.php';

        $this->config = array_merge($default, $config);
    }

    /**
     * 获取当前配置。
     */
    public function config(): array
    {
        return $this->config;
    }

    /**
     * 获取规范化后的本地化语言。
     */
    public function locale(): string
    {
        return $this->normalizeLocale((string) ($this->config['locale'] ?? ''));
    }

    /**
     * 获取规范化后的回退语言。
     */
    public function fallbackLocale(): string
    {
        return $this->normalizeLocale((string) ($this->config['fallback_locale'] ?? ''));
    }

    /**
     * 获取配置的默认时区，未配置时返回 null。
     */
    public function timezone(): ?string
    {
        $timezone = $this->config['timezone'] ?? null;

        if ($timezone instanceof DateTimeZone) {
            return $timezone->getName();
        }

        if (is_string($timezone) && trim($timezone) !== '') {
            return trim($timezone);
        }

        return null;
    }

    /**
     * 将配置的语言应用到 Carbon 与 CarbonImmutable。
     */
    public function applyLocale(): void
    {
        $locale = $this->locale();

        if ($locale !== '') {
            BaseCarbon::setLocale($locale);
            CarbonImmutable::setLocale($locale);
        }

        $fallback = $this->fallbackLocale();

        if ($fallback !== '') {
            BaseCarbon::setFallbackLocale($fallback);
            CarbonImmutable::setFallbackLocale($fallback);
        }
    }

    /**
     * 创建当前时间的 Carbon 实例。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function now($timezone = null): BaseCarbon
    {
        return BaseCarbon::now($this->resolveTimezone($timezone));
    }

    /**
     * 创建今天零点的 Carbon 实例。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function today($timezone = null): BaseCarbon
    {
        return BaseCarbon::today($this->resolveTimezone($timezone));
    }

    /**
     * 创建明天零点的 Carbon 实例。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function tomorrow($timezone = null): BaseCarbon
    {
        return BaseCarbon::tomorrow($this->resolveTimezone($timezone));
    }

    /**
     * 创建昨天零点的 Carbon 实例。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function yesterday($timezone = null): BaseCarbon
    {
        return BaseCarbon::yesterday($this->resolveTimezone($timezone));
    }

    /**
     * 解析日期时间，支持字符串、时间戳、DateTimeInterface 与 null。
     *
     * @param DateTimeInterface|string|int|float|null $time
     * @param DateTimeZone|string|int|null            $timezone
     */
    public function parse($time = null, $timezone = null): BaseCarbon
    {
        return BaseCarbon::parse($time, $this->resolveTimezone($timezone));
    }

    /**
     * parse() 的别名。
     *
     * @param DateTimeInterface|string|int|float|null $time
     * @param DateTimeZone|string|int|null            $timezone
     */
    public function make($time = null, $timezone = null): BaseCarbon
    {
        return $this->parse($time, $timezone);
    }

    /**
     * 解析日期时间并返回不可变的 CarbonImmutable 实例。
     *
     * @param DateTimeInterface|string|int|float|null $time
     * @param DateTimeZone|string|int|null            $timezone
     */
    public function immutable($time = null, $timezone = null): CarbonImmutable
    {
        return CarbonImmutable::parse($time, $this->resolveTimezone($timezone));
    }

    /**
     * 按指定年月日时分秒创建实例。
     *
     * @param DateTimeInterface|string|int|null $year
     * @param DateTimeZone|string|int|null      $timezone
     */
    public function create(
        $year = 0,
        $month = 1,
        $day = 1,
        $hour = 0,
        $minute = 0,
        $second = 0,
        $timezone = null
    ): ?BaseCarbon {
        return BaseCarbon::create(
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $second,
            $this->resolveTimezone($timezone)
        );
    }

    /**
     * 按年月日创建实例，未提供的部分使用当前时间。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function createFromDate($year = null, $month = null, $day = null, $timezone = null): BaseCarbon
    {
        return BaseCarbon::createFromDate($year, $month, $day, $this->resolveTimezone($timezone));
    }

    /**
     * 按时分秒创建实例，未提供的部分使用当前时间。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function createFromTime($hour = 0, $minute = 0, $second = 0, $timezone = null): BaseCarbon
    {
        return BaseCarbon::createFromTime($hour, $minute, $second, $this->resolveTimezone($timezone));
    }

    /**
     * 由秒级时间戳创建实例。
     *
     * @param float|int|string             $timestamp
     * @param DateTimeZone|string|int|null $timezone
     */
    public function createFromTimestamp($timestamp, $timezone = null): BaseCarbon
    {
        return BaseCarbon::createFromTimestamp($timestamp, $this->resolveTimezone($timezone));
    }

    /**
     * 由毫秒级时间戳创建实例。
     *
     * @param float|int|string             $timestamp
     * @param DateTimeZone|string|int|null $timezone
     */
    public function createFromTimestampMs($timestamp, $timezone = null): BaseCarbon
    {
        return BaseCarbon::createFromTimestampMs($timestamp, $this->resolveTimezone($timezone));
    }

    /**
     * 按格式解析日期时间。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function createFromFormat($format, $time, $timezone = null): ?BaseCarbon
    {
        return BaseCarbon::createFromFormat($format, $time, $this->resolveTimezone($timezone));
    }

    /**
     * 按 ISO 格式解析日期时间。
     *
     * @param DateTimeZone|string|int|null $timezone
     */
    public function createFromIsoFormat(
        string $format,
        string $time,
        $timezone = null,
        ?string $locale = null
    ): ?BaseCarbon {
        $locale = $locale ?? $this->locale();

        return BaseCarbon::createFromIsoFormat(
            $format,
            $time,
            $this->resolveTimezone($timezone),
            $locale !== '' ? $locale : null
        );
    }

    /**
     * 代理 Carbon 的其他公开静态方法与已注册的宏，例如 setTestNow()。
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists(BaseCarbon::class, $method)) {
            $reflection = new ReflectionMethod(BaseCarbon::class, $method);

            if (!$reflection->isPublic() || !$reflection->isStatic()) {
                throw new BadMethodCallException(
                    sprintf('Method %s::%s() is not a public static method.', BaseCarbon::class, $method)
                );
            }

            return BaseCarbon::{$method}(...$arguments);
        }

        if (BaseCarbon::hasMacro($method)) {
            return BaseCarbon::{$method}(...$arguments);
        }

        throw new BadMethodCallException(
            sprintf('Method %s::%s() does not exist.', BaseCarbon::class, $method)
        );
    }

    /**
     * 未指定时区时使用配置时区；配置也为空时使用 PHP 默认时区，
     * 即 ThinkPHP 初始化时由 app.default_timezone 写入的时区。
     *
     * @param DateTimeZone|string|int|null $timezone
     * @return DateTimeZone|string|int|null
     */
    protected function resolveTimezone($timezone)
    {
        if ($timezone !== null && $timezone !== '') {
            return $timezone;
        }

        return $this->timezone() ?? date_default_timezone_get();
    }

    /**
     * 将 zh-cn、zh_hans_cn 等写法规范为 Carbon 使用的 zh_CN、zh_Hans_CN。
     */
    protected function normalizeLocale(string $locale): string
    {
        $locale = trim(str_replace('-', '_', $locale));

        if ($locale === '') {
            return '';
        }

        $parts = explode('_', $locale);
        $language = strtolower((string) array_shift($parts));
        $normalized = [$language];

        foreach ($parts as $part) {
            if (strlen($part) === 2) {
                $normalized[] = strtoupper($part);
                continue;
            }

            if (strlen($part) === 4) {
                $normalized[] = ucfirst(strtolower($part));
                continue;
            }

            $normalized[] = $part;
        }

        return implode('_', $normalized);
    }
}