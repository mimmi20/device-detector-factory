<?php
/**
 * This file is part of the mimmi20/device-detector-factory package.
 *
 * Copyright (c) 2022, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Detector;

use DeviceDetector\Cache\PSR16Bridge;
use DeviceDetector\Cache\StaticCache;
use DeviceDetector\DeviceDetector;
use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Laminas\Cache\Storage\Capabilities;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request;
use Mimmi20\Detector\DeviceDetectorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionProperty;

final class DeviceDetectorFactoryTest extends TestCase
{
    private DeviceDetectorFactory $object;

    /**
     * @throws void
     */
    protected function setUp(): void
    {
        $this->object = new DeviceDetectorFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithoutConfig(): void
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
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['Request'], ['config'])
            ->willReturnOnConsecutiveCalls($request, null);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        self::assertInstanceOf(StaticCache::class, $result->getCache());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithEmptyConfig(): void
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

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['Request'], ['config'])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => null]);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        self::assertInstanceOf(StaticCache::class, $result->getCache());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithEmptyConfig2(): void
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

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['Request'], ['config'])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => []]);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        self::assertInstanceOf(StaticCache::class, $result->getCache());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig(): void
    {
        $headerValue = 'test-header';
        $cacheKey    = 'data-cache';
        $config      = [
            'discard-bot-information' => false,
            'skip-bot-detection' => false,
            'cache' => $cacheKey,
        ];

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

        $capabilities = $this->getMockBuilder(Capabilities::class)
            ->disableOriginalConstructor()
            ->getMock();
        $capabilities->expects(self::once())
            ->method('getSupportedDatatypes')
            ->willReturn(['string' => true, 'integer' => true, 'double' => true, 'boolean' => true, 'NULL' => true, 'array' => true, 'object' => true]);
        $capabilities->expects(self::once())
            ->method('getMaxKeyLength')
            ->willReturn(64);

        $cacheStorage = $this->getMockBuilder(StorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::exactly(2))
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $cacheStorage->expects(self::never())
            ->method('getOptions');
        $cacheStorage->expects(self::never())
            ->method('setItem');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive(['Request'], ['config'], [$cacheKey])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config], $cacheStorage);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(PSR16Bridge::class, $psr16Cache);

        $simple = new ReflectionProperty($psr16Cache, 'cache');
        $simple->setAccessible(true);

        $simpleCache = $simple->getValue($psr16Cache);

        self::assertInstanceOf(SimpleCacheDecorator::class, $simpleCache);

        $storage = new ReflectionProperty($simpleCache, 'storage');
        $storage->setAccessible(true);

        self::assertSame($cacheStorage, $storage->getValue($simpleCache));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig2(): void
    {
        $headerValue = 'test-header';
        $cacheKey    = 'data-cache';
        $config      = [
            'discard-bot-information' => true,
            'skip-bot-detection' => true,
            'cache' => $cacheKey,
        ];

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

        $capabilities = $this->getMockBuilder(Capabilities::class)
            ->disableOriginalConstructor()
            ->getMock();
        $capabilities->expects(self::once())
            ->method('getSupportedDatatypes')
            ->willReturn(['string' => true, 'integer' => true, 'double' => true, 'boolean' => true, 'NULL' => true, 'array' => true, 'object' => true]);
        $capabilities->expects(self::once())
            ->method('getMaxKeyLength')
            ->willReturn(64);

        $cacheStorage = $this->getMockBuilder(StorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::exactly(2))
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $cacheStorage->expects(self::never())
            ->method('getOptions');
        $cacheStorage->expects(self::never())
            ->method('setItem');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive(['Request'], ['config'], [$cacheKey])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config], $cacheStorage);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertTrue($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertTrue($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(PSR16Bridge::class, $psr16Cache);

        $simple = new ReflectionProperty($psr16Cache, 'cache');
        $simple->setAccessible(true);

        $simpleCache = $simple->getValue($psr16Cache);

        self::assertInstanceOf(SimpleCacheDecorator::class, $simpleCache);

        $storage = new ReflectionProperty($simpleCache, 'storage');
        $storage->setAccessible(true);

        self::assertSame($cacheStorage, $storage->getValue($simpleCache));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig3(): void
    {
        $headerValue = 'test-header';
        $cacheKey    = 'data-cache';
        $config      = [
            'discard-bot-information' => 1,
            'skip-bot-detection' => 1,
            'cache' => $cacheKey,
        ];

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

        $capabilities = $this->getMockBuilder(Capabilities::class)
            ->disableOriginalConstructor()
            ->getMock();
        $capabilities->expects(self::once())
            ->method('getSupportedDatatypes')
            ->willReturn(['string' => true, 'integer' => true, 'double' => true, 'boolean' => true, 'NULL' => true, 'array' => true, 'object' => true]);
        $capabilities->expects(self::once())
            ->method('getMaxKeyLength')
            ->willReturn(64);

        $cacheStorage = $this->getMockBuilder(StorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::exactly(2))
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $cacheStorage->expects(self::never())
            ->method('getOptions');
        $cacheStorage->expects(self::never())
            ->method('setItem');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive(['Request'], ['config'], [$cacheKey])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config], $cacheStorage);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertTrue($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertTrue($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(PSR16Bridge::class, $psr16Cache);

        $simple = new ReflectionProperty($psr16Cache, 'cache');
        $simple->setAccessible(true);

        $simpleCache = $simple->getValue($psr16Cache);

        self::assertInstanceOf(SimpleCacheDecorator::class, $simpleCache);

        $storage = new ReflectionProperty($simpleCache, 'storage');
        $storage->setAccessible(true);

        self::assertSame($cacheStorage, $storage->getValue($simpleCache));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig4(): void
    {
        $headerValue = 'test-header';
        $cacheKey    = 'data-cache';
        $config      = [
            'discard-bot-information' => 0,
            'skip-bot-detection' => 0,
            'cache' => $cacheKey,
        ];

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

        $capabilities = $this->getMockBuilder(Capabilities::class)
            ->disableOriginalConstructor()
            ->getMock();
        $capabilities->expects(self::once())
            ->method('getSupportedDatatypes')
            ->willReturn(['string' => true, 'integer' => true, 'double' => true, 'boolean' => true, 'NULL' => true, 'array' => true, 'object' => true]);
        $capabilities->expects(self::once())
            ->method('getMaxKeyLength')
            ->willReturn(64);

        $cacheStorage = $this->getMockBuilder(StorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::exactly(2))
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $cacheStorage->expects(self::never())
            ->method('getOptions');
        $cacheStorage->expects(self::never())
            ->method('setItem');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive(['Request'], ['config'], [$cacheKey])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config], $cacheStorage);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(PSR16Bridge::class, $psr16Cache);

        $simple = new ReflectionProperty($psr16Cache, 'cache');
        $simple->setAccessible(true);

        $simpleCache = $simple->getValue($psr16Cache);

        self::assertInstanceOf(SimpleCacheDecorator::class, $simpleCache);

        $storage = new ReflectionProperty($simpleCache, 'storage');
        $storage->setAccessible(true);

        self::assertSame($cacheStorage, $storage->getValue($simpleCache));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig5(): void
    {
        $headerValue = 'test-header';
        $cacheKey    = 'data-cache';
        $config      = [
            'discard-bot-information' => 0,
            'skip-bot-detection' => 0,
            'cache' => $cacheKey,
        ];

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

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive(['Request'], ['config'], [$cacheKey])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config], null);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        self::assertInstanceOf(StaticCache::class, $result->getCache());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig6(): void
    {
        $headerValue = 'test-header';

        $capabilities = $this->getMockBuilder(Capabilities::class)
            ->disableOriginalConstructor()
            ->getMock();
        $capabilities->expects(self::once())
            ->method('getSupportedDatatypes')
            ->willReturn(['string' => true, 'integer' => true, 'double' => true, 'boolean' => true, 'NULL' => true, 'array' => true, 'object' => true]);
        $capabilities->expects(self::once())
            ->method('getMaxKeyLength')
            ->willReturn(64);

        $cacheStorage = $this->getMockBuilder(StorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheStorage->expects(self::exactly(2))
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $cacheStorage->expects(self::never())
            ->method('getOptions');
        $cacheStorage->expects(self::never())
            ->method('setItem');

        $config = [
            'discard-bot-information' => 0,
            'skip-bot-detection' => 0,
            'cache' => $cacheStorage,
        ];

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

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['Request'], ['config'])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config]);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        $psr16Cache = $result->getCache();

        self::assertInstanceOf(PSR16Bridge::class, $psr16Cache);

        $simple = new ReflectionProperty($psr16Cache, 'cache');
        $simple->setAccessible(true);

        $simpleCache = $simple->getValue($psr16Cache);

        self::assertInstanceOf(SimpleCacheDecorator::class, $simpleCache);

        $storage = new ReflectionProperty($simpleCache, 'storage');
        $storage->setAccessible(true);

        self::assertSame($cacheStorage, $storage->getValue($simpleCache));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithConfig7(): void
    {
        $headerValue = 'test-header';
        $config      = [
            'discard-bot-information' => 0,
            'skip-bot-detection' => 0,
            'cache' => null,
        ];

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

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['Request'], ['config'])
            ->willReturnOnConsecutiveCalls($request, ['device-detector' => $config]);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(DeviceDetector::class, $result);

        self::assertSame($headerValue, $result->getUserAgent());

        $discard = new ReflectionProperty($result, 'discardBotInformation');
        $discard->setAccessible(true);

        self::assertFalse($discard->getValue($result));

        $skip = new ReflectionProperty($result, 'skipBotDetection');
        $skip->setAccessible(true);

        self::assertFalse($skip->getValue($result));

        self::assertInstanceOf(StaticCache::class, $result->getCache());
    }
}
