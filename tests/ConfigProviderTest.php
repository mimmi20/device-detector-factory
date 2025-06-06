<?php

/**
 * This file is part of the mimmi20/device-detector-factory package.
 *
 * Copyright (c) 2022-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Detector;

use DeviceDetector\DeviceDetector;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    /** @throws Exception */
    public function testGetDependencyConfig(): void
    {
        $dependencyConfig = (new ConfigProvider())->getDependencyConfig();
        self::assertIsArray($dependencyConfig);
        self::assertCount(1, $dependencyConfig);

        self::assertArrayNotHasKey('delegators', $dependencyConfig);
        self::assertArrayNotHasKey('initializers', $dependencyConfig);
        self::assertArrayNotHasKey('invokables', $dependencyConfig);
        self::assertArrayNotHasKey('services', $dependencyConfig);
        self::assertArrayNotHasKey('shared', $dependencyConfig);
        self::assertArrayNotHasKey('abstract_factories', $dependencyConfig);
        self::assertArrayNotHasKey('aliases', $dependencyConfig);

        self::assertArrayHasKey('factories', $dependencyConfig);
        $factories = $dependencyConfig['factories'];
        self::assertIsArray($factories);
        self::assertCount(1, $factories);
        self::assertArrayHasKey(DeviceDetector::class, $factories);
    }

    /** @throws Exception */
    public function testInvocationReturnsArrayWithDependencies(): void
    {
        $config = (new ConfigProvider())();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('dependencies', $config);

        $dependencyConfig = $config['dependencies'];
        self::assertIsArray($dependencyConfig);
        self::assertCount(1, $dependencyConfig);

        self::assertArrayNotHasKey('delegators', $dependencyConfig);
        self::assertArrayNotHasKey('initializers', $dependencyConfig);
        self::assertArrayNotHasKey('invokables', $dependencyConfig);
        self::assertArrayNotHasKey('services', $dependencyConfig);
        self::assertArrayNotHasKey('shared', $dependencyConfig);
        self::assertArrayNotHasKey('abstract_factories', $dependencyConfig);
        self::assertArrayNotHasKey('aliases', $dependencyConfig);

        self::assertArrayHasKey('factories', $dependencyConfig);
        $factories = $dependencyConfig['factories'];
        self::assertIsArray($factories);
        self::assertCount(1, $factories);
        self::assertArrayHasKey(DeviceDetector::class, $factories);
    }

    /** @throws Exception */
    public function testInvocationReturnsArrayWithDependencies2(): void
    {
        $config = (new ConfigProvider())();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('dependencies', $config);

        $dependencyConfig = $config['dependencies'];
        self::assertIsArray($dependencyConfig);
        self::assertCount(1, $dependencyConfig);

        self::assertArrayNotHasKey('delegators', $dependencyConfig);
        self::assertArrayNotHasKey('initializers', $dependencyConfig);
        self::assertArrayNotHasKey('invokables', $dependencyConfig);
        self::assertArrayNotHasKey('services', $dependencyConfig);
        self::assertArrayNotHasKey('shared', $dependencyConfig);
        self::assertArrayNotHasKey('abstract_factories', $dependencyConfig);
        self::assertArrayNotHasKey('aliases', $dependencyConfig);

        self::assertArrayHasKey('factories', $dependencyConfig);
        $factories = $dependencyConfig['factories'];
        self::assertIsArray($factories);
        self::assertCount(1, $factories);
        self::assertArrayHasKey(DeviceDetector::class, $factories);
    }
}
