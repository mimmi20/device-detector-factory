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

use AssertionError;
use DeviceDetector\Cache\PSR16Bridge;
use DeviceDetector\Cache\StaticCache;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\CacheInterface;
use ReflectionException;
use ReflectionProperty;

final class DeviceDetectorFactoryTest extends TestCase
{
    private DeviceDetectorFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new DeviceDetectorFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithoutRequest(): void
    {
        $exceptionMessage = 'test-message';
        $exception        = new ServiceNotFoundException($exceptionMessage);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::never())
            ->method('getHeaders');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with('Request')
            ->willThrowException($exception);
        $container->expects(self::never())
            ->method('has');

        try {
            ($this->object)($container, '');

            self::fail('ServiceNotCreatedException expected');
        } catch (ServiceNotCreatedException $e) {
            self::assertSame(0, $e->getCode());
            self::assertSame($exceptionMessage, $e->getMessage());
            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithoutRequest2(): void
    {
        $exceptionMessage = 'test-message';
        $exception        = new ServiceNotCreatedException($exceptionMessage);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::never())
            ->method('getHeaders');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with('Request')
            ->willThrowException($exception);
        $container->expects(self::never())
            ->method('has');

        try {
            ($this->object)($container, '');

            self::fail('ServiceNotCreatedException expected');
        } catch (ServiceNotCreatedException $e) {
            self::assertSame(0, $e->getCode());
            self::assertSame($exceptionMessage, $e->getMessage());
            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithoutConfig(): void
    {
        $exceptionMessage = 'test-message';
        $exception        = new ServiceNotFoundException($exceptionMessage);

        $headers = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects(self::never())
            ->method('get');
        $headers->expects(self::once())
            ->method('has')
            ->with('user-agent')
            ->willReturn(false);
        $headers->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn($headers);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $param) use ($request, $matcher, $exception): Request {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('Request', $param),
                        2 => self::assertSame(ConfigInterface::class, $param),
                    };

                    if ($matcher->numberOfInvocations() === 1) {
                        return $request;
                    }

                    throw $exception;
                },
            );
        $container->expects(self::never())
            ->method('has');

        try {
            ($this->object)($container, '');

            self::fail('ServiceNotCreatedException expected');
        } catch (ServiceNotCreatedException $e) {
            self::assertSame(0, $e->getCode());
            self::assertSame($exceptionMessage, $e->getMessage());
            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithoutConfig2(): void
    {
        $exceptionMessage = 'test-message';
        $exception        = new ServiceNotCreatedException($exceptionMessage);

        $headers = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects(self::never())
            ->method('get');
        $headers->expects(self::once())
            ->method('has')
            ->with('user-agent')
            ->willReturn(false);
        $headers->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn($headers);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $param) use ($request, $matcher, $exception): Request {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('Request', $param),
                        2 => self::assertSame(ConfigInterface::class, $param),
                    };

                    if ($matcher->numberOfInvocations() === 1) {
                        return $request;
                    }

                    throw $exception;
                },
            );
        $container->expects(self::never())
            ->method('has');

        try {
            ($this->object)($container, '');

            self::fail('ServiceNotCreatedException expected');
        } catch (ServiceNotCreatedException $e) {
            self::assertSame(0, $e->getCode());
            self::assertSame($exceptionMessage, $e->getMessage());
            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithoutConfig3(): void
    {
        $headers = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects(self::never())
            ->method('get');
        $headers->expects(self::once())
            ->method('has')
            ->with('user-agent')
            ->willReturn(false);
        $headers->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn($headers);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $param) use ($request, $matcher): Request | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('Request', $param),
                        2 => self::assertSame(ConfigInterface::class, $param),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $request,
                        2 => null,
                    };
                },
            );
        $container->expects(self::never())
            ->method('has');

        try {
            ($this->object)($container, '');

            self::fail('AssertionError expected');
        } catch (AssertionError $e) {
            self::assertSame(1, $e->getCode());
            self::assertSame('assert($config instanceof ConfigInterface)', $e->getMessage());
            self::assertNull($e->getPrevious());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithoutCache(): void
    {
        $headerValue = 'test-header';

        $header = $this->getMockBuilder(HeaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $header->expects(self::once())
            ->method('getFieldValue')
            ->willReturn($headerValue);

        $headers = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects(self::once())
            ->method('get')
            ->with('user-agent')
            ->willReturn($header);
        $headers->expects(self::once())
            ->method('has')
            ->with('user-agent')
            ->willReturn(true);
        $headers->expects(self::once())
            ->method('toArray')
            ->willReturn(['user-agent' => $headerValue]);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn($headers);

        $cacheStorage = $this->getMockBuilder(StorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::never())
            ->method('getCapabilities');
        $cacheStorage->expects(self::never())
            ->method('getOptions');
        $cacheStorage->expects(self::never())
            ->method('setItem');

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getCache')
            ->willReturn(null);
        $config->expects(self::once())
            ->method('discardBotInformation')
            ->willReturn(false);
        $config->expects(self::once())
            ->method('skipBotDetection')
            ->willReturn(false);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $param) use ($request, $matcher, $config): Request | ConfigInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('Request', $param),
                        2 => self::assertSame(ConfigInterface::class, $param),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $request,
                        2 => $config,
                    };
                },
            );
        $container->expects(self::never())
            ->method('has');

        $result = ($this->object)($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');

        self::assertFalse($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(StaticCache::class, $psr16Cache);

        $hint = new ReflectionProperty($result, 'clientHints');

        $clientHints = $hint->getValue($result);

        self::assertInstanceOf(ClientHints::class, $clientHints);
        self::assertSame([], $clientHints->getBrandList());
        self::assertSame('', $clientHints->getModel());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithCache(): void
    {
        $headerValue = 'test-header';

        $header = $this->getMockBuilder(HeaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $header->expects(self::once())
            ->method('getFieldValue')
            ->willReturn($headerValue);

        $headers = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects(self::once())
            ->method('get')
            ->with('user-agent')
            ->willReturn($header);
        $headers->expects(self::once())
            ->method('has')
            ->with('user-agent')
            ->willReturn(true);
        $headers->expects(self::once())
            ->method('toArray')
            ->willReturn(['user-agent' => $headerValue]);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn($headers);

        $cacheStorage = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::never())
            ->method('get');
        $cacheStorage->expects(self::never())
            ->method('set');

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getCache')
            ->willReturn($cacheStorage);
        $config->expects(self::once())
            ->method('discardBotInformation')
            ->willReturn(true);
        $config->expects(self::once())
            ->method('skipBotDetection')
            ->willReturn(true);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher   = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $param) use ($request, $matcher, $config): Request | ConfigInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('Request', $param),
                        2 => self::assertSame(ConfigInterface::class, $param),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $request,
                        2 => $config,
                    };
                },
            );
        $container->expects(self::never())
            ->method('has');

        $result = ($this->object)($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');

        self::assertTrue($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');

        self::assertTrue($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(PSR16Bridge::class, $psr16Cache);

        $simple = new ReflectionProperty($psr16Cache, 'cache');

        $simpleCache = $simple->getValue($psr16Cache);

        self::assertSame($cacheStorage, $simpleCache);

        $hint = new ReflectionProperty($result, 'clientHints');

        $clientHints = $hint->getValue($result);

        self::assertInstanceOf(ClientHints::class, $clientHints);
        self::assertSame([], $clientHints->getBrandList());
        self::assertSame('', $clientHints->getModel());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithWrongRequest(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with('Request')
            ->willReturn(null);
        $container->expects(self::never())
            ->method('has');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($request instanceof Request)');

        ($this->object)($container, '');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithWrongHeaders(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn(null);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with('Request')
            ->willReturn($request);
        $container->expects(self::never())
            ->method('has');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($headers instanceof Headers)');

        ($this->object)($container, '');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithWrongHeader(): void
    {
        $headers = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects(self::once())
            ->method('get')
            ->with('user-agent')
            ->willReturn(null);
        $headers->expects(self::once())
            ->method('has')
            ->with('user-agent')
            ->willReturn(true);
        $headers->expects(self::never())
            ->method('toArray');

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHeaders')
            ->with(null, false)
            ->willReturn($headers);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with('Request')
            ->willReturn($request);
        $container->expects(self::never())
            ->method('has');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($uaHader instanceof HeaderInterface)');

        ($this->object)($container, '');
    }
}
