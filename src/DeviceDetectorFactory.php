<?php
/**
 * This file is part of the mimmi20/device-detector-factory package.
 *
 * Copyright (c) 2022-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Detector;

use DeviceDetector\Cache\PSR16Bridge;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function assert;
use function is_array;
use function is_string;

final class DeviceDetectorFactory implements FactoryInterface
{
    /**
     * @param string            $requestedName
     * @param array<mixed>|null $options
     * @phpstan-param array<mixed>|null $options
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array | null $options = null,
    ): DeviceDetector {
        $request = $container->get('Request');
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

        $config = $container->get('config');
        assert(is_array($config) || null === $config);
        $config = $config['device-detector'] ?? [];
        assert(is_array($config));

        if (array_key_exists('cache', $config)) {
            if (is_string($config['cache'])) {
                $cacheStorage = $container->get($config['cache']);
            } else {
                $cacheStorage = $config['cache'];
            }

            if ($cacheStorage instanceof StorageInterface) {
                $detector->setCache(new PSR16Bridge(new SimpleCacheDecorator($cacheStorage)));
            }
        }

        if (array_key_exists('discard-bot-information', $config)) {
            $detector->discardBotInformation((bool) $config['discard-bot-information']);
        }

        if (array_key_exists('skip-bot-detection', $config)) {
            $detector->skipBotDetection((bool) $config['skip-bot-detection']);
        }

        return $detector;
    }
}
