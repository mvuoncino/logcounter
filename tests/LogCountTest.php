<?php

namespace MVuoncino\LogCounter\Tests;

use Illuminate\Cache\StoreInterface;
use Monolog\Logger;
use MVuoncino\LogCounter\Models\LogCountHandler;
use PHPUnit\Framework\TestCase;
use Mockery as M;

class LogCountTest extends TestCase
{
    public function testHandler()
    {
        $store = M::mock(StoreInterface::class);
        $store->shouldReceive('increment')->once()->andReturnUndefined();
        $store->shouldReceive('put')->once()->andReturnUndefined();
        $handler = new LogCountHandler();
        $handler->setStore($store);

        $monolog = new Logger('test', [$handler]);
        $monolog->log(Logger::INFO,'Test Message');
    }
}