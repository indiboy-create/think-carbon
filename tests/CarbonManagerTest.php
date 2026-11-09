<?php

declare(strict_types=1);

namespace Youvision\ThinkCarbon\Tests;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use think\Container;
use Youvision\ThinkCarbon\CarbonManager;

class CarbonManagerTest extends TestCase
{
    protected function setUp(): void
    {
        BaseCarbon::setTestNow(BaseCarbon::parse('2024-01-15 10:30:00'));
    }

    protected function tearDown(): void
    {
        BaseCarbon::setTestNow();
        Container::setInstance(null);
    }

    public function testDefaultConfig(): void
    {
        $manager = new CarbonManager();

        $this->assertSame('zh_CN', $manager->locale());
        $this->assertSame('en', $manager->fallbackLocale());
        $this->assertNull($manager->timezone());
        $this->assertSame('zh_CN', $manager->config()['locale']);
        $this->assertSame(
            date_default_timezone_get(),
            $manager->createFromTimestamp(0)->getTimezone()->getName()
        );
    }

    public function testConfiguredTimezoneIsApplied(): void
    {
        $manager = new CarbonManager(['timezone' => 'Asia/Shanghai']);

        $this->assertSame('Asia/Shanghai', $manager->now()->getTimezone()->getName());
        $this->assertSame('Asia/Shanghai', $manager->today()->getTimezone()->getName());
        $this->assertSame(
            '2024-01-02 03:04:05',
            $manager->parse('2024-01-02 03:04:05')->format('Y-m-d H:i:s')
        );
        $this->assertSame('Asia/Shanghai', $manager->createFromTimestamp(1700000000)->getTimezone()->getName());
    }

    public function testExplicitTimezoneOverridesConfig(): void
    {
        $manager = new CarbonManager(['timezone' => 'Asia/Shanghai']);

        $this->assertSame('UTC', $manager->parse('2024-01-02 03:04:05', 'UTC')->getTimezone()->getName());
        $this->assertSame('UTC', $manager->now('UTC')->getTimezone()->getName());
    }

    public function testImmutableAndFactoryMethods(): void
    {
        $manager = new CarbonManager(['timezone' => 'Asia/Tokyo']);

        $immutable = $manager->immutable('2024-02-03 04:05:06');
        $this->assertInstanceOf(CarbonImmutable::class, $immutable);
        $this->assertSame('2024-02-03 04:05:06', $immutable->format('Y-m-d H:i:s'));

        $created = $manager->create(2024, 2, 3, 4, 5, 6);
        $this->assertInstanceOf(BaseCarbon::class, $created);
        $this->assertSame('2024-02-03 04:05:06', $created->format('Y-m-d H:i:s'));

        $fromDate = $manager->createFromDate(2024, 2, 3);
        $this->assertSame('2024-02-03', $fromDate->format('Y-m-d'));

        $fromFormat = $manager->createFromFormat('Y/m/d H:i', '2024/02/03 04:05');
        $this->assertInstanceOf(BaseCarbon::class, $fromFormat);
        $this->assertSame('2024-02-03 04:05:00', $fromFormat->format('Y-m-d H:i:s'));
    }

    public function testLocaleNormalization(): void
    {
        $this->assertSame('zh_CN', (new CarbonManager(['locale' => 'zh-cn']))->locale());
        $this->assertSame('zh_Hans_CN', (new CarbonManager(['locale' => 'zh-hans-cn']))->locale());
        $this->assertSame('zh_TW', (new CarbonManager(['locale' => 'ZH-tw']))->locale());
        $this->assertSame('', (new CarbonManager(['locale' => '']))->locale());
    }

    public function testApplyLocale(): void
    {
        (new CarbonManager(['locale' => 'zh_CN', 'fallback_locale' => 'en']))->applyLocale();

        $this->assertSame('zh_CN', BaseCarbon::getLocale());
        $this->assertSame('zh_CN', CarbonImmutable::getLocale());
        $this->assertStringContainsString(
            '年',
            BaseCarbon::parse('2023-01-15 10:30:00')->diffForHumans()
        );
    }

    public function testStaticProxy(): void
    {
        $manager = new CarbonManager();

        $manager->setTestNow(BaseCarbon::parse('2024-03-01 08:00:00'));

        $this->assertTrue($manager->hasTestNow());
        $this->assertSame('2024-03-01', BaseCarbon::now()->format('Y-m-d'));

        BaseCarbon::macro('thinkCarbonMacro', static fn (): string => 'macro-ok');
        $this->assertSame('macro-ok', $manager->thinkCarbonMacro());
    }

    public function testHelperFunctions(): void
    {
        $container = new Container();
        $container->bind(CarbonManager::class, static function (): CarbonManager {
            return new CarbonManager(['timezone' => 'UTC']);
        });
        Container::setInstance($container);

        $carbon = carbon('2024-01-02 03:04:05');
        $this->assertInstanceOf(BaseCarbon::class, $carbon);
        $this->assertSame('UTC', $carbon->getTimezone()->getName());

        $this->assertInstanceOf(CarbonImmutable::class, carbon_immutable('2024-01-02'));
        $this->assertSame('UTC', carbon_now()->getTimezone()->getName());
    }
}