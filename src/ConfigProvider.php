<?php

/**
 * This file is part of the mimmi20/device-detector-factory package.
 *
 * Copyright (c) 2022-2026, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Detector;

use DeviceDetector\DeviceDetector;

final class ConfigProvider
{
    /**
     * Return general-purpose laminas-navigation configuration.
     *
     * @return array<string, array<string, array<int|string, string>>>
     * @phpstan-return array{dependencies: array{factories: array<class-string, class-string>}}
     *
     * @throws void
     */
    public function __invoke(): array
    {
        return ['dependencies' => $this->getDependencyConfig()];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return array<string, array<int|string, string>>
     * @phpstan-return array{factories: array<class-string, class-string>}
     *
     * @throws void
     */
    public function getDependencyConfig(): array
    {
        return ['factories' => [DeviceDetector::class => DeviceDetectorFactory::class]];
    }
}
