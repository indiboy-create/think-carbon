<?php

declare(strict_types=1);

namespace Youvision\ThinkCarbon\Tests;

use Carbon\Carbon as BaseCarbon;
use PHPUnit\Framework\TestCase;
use think\App;
use Youvision\ThinkCarbon\CarbonManager;
use Youvision\ThinkCarbon\Service;

class ServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        BaseCarbon::setLocale('en');
    }

    public function testServiceRegistersManagerAndConfig(): void
    {
        $app = new App(dirname(__DIR__));
        $app->config->set(['default_timezone' => 'Asia/Shanghai'], 'app');
        $app->register(Service::class);
        $app->boot();

        $manager = $app->make(CarbonManager::class);

        $this->assertInstanceOf(CarbonManager::class, $manager);
        $this->assertSame($manager, $app->make('carbon'));
        $this->assertSame('zh_CN', $app->config->get('carbon.locale'));
        $this->assertSame('en', $app->config->get('carbon.fallback_locale'));
        $this->assertNull($app->config->get('carbon.timezone'));
        $this->assertSame('zh_CN', BaseCarbon::getLocale());
        $this->assertInstanceOf(BaseCarbon::class, \Youvision\ThinkCarbon\Facade\Carbon::now());
    }

    public function testApplicationConfigOverridesDefaults(): void
    {
        $app = new App(dirname(__DIR__));
        $app->config->set(['default_timezone' => 'UTC'], 'app');
        $app->config->set([
            'locale' => 'en',
            'timezone' => 'Asia/Tokyo',
        ], 'carbon');
        $app->register(Service::class);
        $app->boot();

        $manager = $app->make('carbon');

        $this->assertSame('en', $app->config->get('carbon.locale'));
        $this->assertSame('Asia/Tokyo', $app->config->get('carbon.timezone'));
        $this->assertSame('Asia/Tokyo', $manager->timezone());
        $this->assertSame('Asia/Tokyo', $manager->now()->getTimezone()->getName());
        $this->assertSame('en', BaseCarbon::getLocale());
    }
}