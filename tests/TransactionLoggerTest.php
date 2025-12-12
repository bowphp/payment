<?php

namespace Bow\Payment\Tests;

use PHPUnit\Framework\TestCase;
use Bow\Payment\Support\TransactionLogger;

class TransactionLoggerTest extends TestCase
{
    private $logPath;
    private $logger;

    protected function setUp(): void
    {
        $this->logPath = sys_get_temp_dir() . '/test_payment_logs_' . uniqid();
        $this->logger = new TransactionLogger($this->logPath, true);
    }

    protected function tearDown(): void
    {
        // Clean up log files
        if (file_exists($this->logPath)) {
            $files = glob($this->logPath . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->logPath);
        }
    }

    public function testLoggerCreatesDirectory()
    {
        $this->assertDirectoryExists($this->logPath);
    }

    public function testLogInfo()
    {
        $this->logger->info('Test info message', ['key' => 'value']);
        
        $logFile = $this->logPath . '/payment_' . date('Y-m-d') . '.log';
        $this->assertFileExists($logFile);
        
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('Test info message', $content);
        $this->assertStringContainsString('info', $content);
    }

    public function testLogPaymentRequest()
    {
        $this->logger->logPaymentRequest('orange', [
            'amount' => 1000,
            'reference' => 'TEST-123',
        ]);
        
        $logFile = $this->logPath . '/payment_' . date('Y-m-d') . '.log';
        $content = file_get_contents($logFile);
        
        $this->assertStringContainsString('Payment request initiated', $content);
        $this->assertStringContainsString('orange', $content);
        $this->assertStringContainsString('1000', $content);
    }

    public function testDisabledLogger()
    {
        $logger = new TransactionLogger($this->logPath, false);
        $logger->info('This should not be logged');
        
        $logFile = $this->logPath . '/payment_' . date('Y-m-d') . '.log';
        $this->assertFileDoesNotExist($logFile);
    }
}
