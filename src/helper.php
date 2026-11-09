<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Youvision\ThinkCarbon\CarbonManager;

if (!function_exists('carbon')) {
    /**
     * 解析日期时间并返回可变的 Carbon 实例。
     *
     * @param \DateTimeInterface|string|int|float|null $time
     * @param \DateTimeZone|string|int|null            $timezone
     */
    function carbon($time = null, $timezone = null): Carbon
    {
        return app(CarbonManager::class)->parse($time, $timezone);
    }
}

if (!function_exists('carbon_immutable')) {
    /**
     * 解析日期时间并返回不可变的 CarbonImmutable 实例。
     *
     * @param \DateTimeInterface|string|int|float|null $time
     * @param \DateTimeZone|string|int|null            $timezone
     */
    function carbon_immutable($time = null, $timezone = null): CarbonImmutable
    {
        return app(CarbonManager::class)->immutable($time, $timezone);
    }
}

if (!function_exists('carbon_now')) {
    /**
     * 获取当前时间的 Carbon 实例。
     *
     * @param \DateTimeZone|string|int|null $timezone
     */
    function carbon_now($timezone = null): Carbon
    {
        return app(CarbonManager::class)->now($timezone);
    }
}