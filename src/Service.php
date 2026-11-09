<?php

declare(strict_types=1);

namespace Youvision\ThinkCarbon;

use think\Service as ThinkService;

/**
 * ThinkPHP 服务注册。
 */
class Service extends ThinkService
{
    public function register(): void
    {
        $default = require dirname(__DIR__) . '/config/carbon.php';
        $current = (array) $this->app->config->get('carbon', []);

        $this->app->config->set(array_merge($default, $current), 'carbon');

        $this->app->bind('carbon', function () {
            return new CarbonManager((array) $this->app->config->get('carbon', []));
        });

        $this->app->bind(CarbonManager::class, 'carbon');
    }

    public function boot(): void
    {
        $this->app->make(CarbonManager::class)->applyLocale();
    }
}