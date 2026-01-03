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

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

interface ConfigInterface
{
    /** @throws void */
    public function getCache(): CacheItemPoolInterface | CacheInterface | null;

    /** @throws void */
    public function discardBotInformation(): bool;

    /** @throws void */
    public function skipBotDetection(): bool;
}
