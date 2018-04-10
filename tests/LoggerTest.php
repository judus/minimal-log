<?php namespace Maduser\Minimal\Events\Tests;

use Maduser\Minimal\Log\Logger;
use PHPUnit\Framework\TestCase;

/**
 * Class DispatcherTest
 *
 * @package Maduser\Minimal\Events\Tests
 */
class LoggerTest extends TestCase
{
    /** @var Logger */
    private $logger;

    /**
     *
     */
    public function setUp()
    {
        $this->logger = new Logger(__DIR__ . '/../testdir', 5, true);
    }

    /** @test */
    public function loggerTest()
    {
        $this->logger->debug('Hello');
        $this->logger->system('Hello');
        $this->logger->info('Hello');
        $this->logger->warn('Hello');
        $this->logger->error('Hello');
    }
}
