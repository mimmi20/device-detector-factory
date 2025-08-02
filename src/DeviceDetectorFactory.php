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

use DeviceDetector\Cache\PSR16Bridge;
use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

use function assert;

final class DeviceDetectorFactory implements FactoryInterface
{
    /**
     * @param string            $requestedName
     * @param array<mixed>|null $options
     * @phpstan-param array<mixed>|null $options
     *
     * @throws ServiceNotCreatedException
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array | null $options = null,
    ): DeviceDetector {
        try {
            $request = $container->get('Request');
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException($e->getMessage(), $e->getCode(), $e);
        }

        assert($request instanceof Request);

        $detector = new DeviceDetector();
        $headers  = $request->getHeaders();
        assert($headers instanceof Headers);

        if ($headers->has('user-agent')) {
            $uaHader = $headers->get('user-agent');
            assert($uaHader instanceof HeaderInterface);
            $detector->setUserAgent($uaHader->getFieldValue());
        }

        $clientHints = ClientHints::factory($headers->toArray());
        $detector->setClientHints($clientHints);

        try {
            $config = $container->get(ConfigInterface::class);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException($e->getMessage(), $e->getCode(), $e);
        }

        assert($config instanceof ConfigInterface);

        $cacheStorage = $config->getCache();

        if ($cacheStorage instanceof CacheInterface) {
            $detector->setCache(new PSR16Bridge($cacheStorage));
        } elseif ($cacheStorage instanceof CacheItemPoolInterface) {
            $detector->setCache(new PSR6Bridge($cacheStorage));
        }

        $detector->discardBotInformation($config->discardBotInformation());
        $detector->skipBotDetection($config->skipBotDetection());

        return $detector;
    }
}
