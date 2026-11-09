<?php

declare(strict_types=1);

namespace Youvision\ThinkCarbon\Facade;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable;
use think\Facade;

/**
 * @method static \Carbon\Carbon now(\DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\Carbon today(\DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\Carbon tomorrow(\DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\Carbon yesterday(\DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\Carbon parse(\DateTimeInterface|string|int|float|null $time = null, \DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\Carbon make(\DateTimeInterface|string|int|float|null $time = null, \DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\CarbonImmutable immutable(\DateTimeInterface|string|int|float|null $time = null, \DateTimeZone|string|int|null $timezone = null)
 * @method static \Carbon\Carbon|null create($year = 0, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0, $timezone = null)
 * @method static \Carbon\Carbon createFromDate($year = null, $month = null, $day = null, $timezone = null)
 * @method static \Carbon\Carbon createFromTime($hour = 0, $minute = 0, $second = 0, $timezone = null)
 * @method static \Carbon\Carbon createFromTimestamp($timestamp, $timezone = null)
 * @method static \Carbon\Carbon createFromTimestampMs($timestamp, $timezone = null)
 * @method static \Carbon\Carbon|null createFromFormat($format, $time, $timezone = null)
 * @method static \Carbon\Carbon|null createFromIsoFormat(string $format, string $time, $timezone = null, ?string $locale = null)
 * @method static string locale()
 * @method static string fallbackLocale()
 * @method static string|null timezone()
 * @method static array config()
 *

 * @method static void setLocale(string $locale)
 * @method static string getLocale()
 * @method static void setFallbackLocale(string $locale)
 * @method static void setTestNow(mixed $testNow = null)
 * @method static \Carbon\Carbon|null getTestNow()
 * @method static bool hasTestNow()
 * @method static void macro(string $name, ?callable $macro)
 * @method static bool hasMacro(string $name)
 *
 * @see \Youvision\ThinkCarbon\CarbonManager
 */
class Carbon extends Facade
{
    protected static function getFacadeClass()
    {
        return 'carbon';
    }
}