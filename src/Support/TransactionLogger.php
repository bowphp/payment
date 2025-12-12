<?php

namespace Bow\Payment\Support;

/**
 * Transaction logger for audit trails
 */
class TransactionLogger
{
    /**
     * Log levels
     */
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const ERROR = 'error';
    public const SUCCESS = 'success';

    /**
     * Log storage path
     *
     * @var string
     */
    private $logPath;

    /**
     * Whether logging is enabled
     *
     * @var bool
     */
    private $enabled;

    /**
     * Create a new transaction logger
     *
     * @param string $logPath
     * @param bool $enabled
     */
    public function __construct(string $logPath = '', bool $enabled = true)
    {
        $this->logPath = $logPath ?: sys_get_temp_dir() . '/bow_payment_logs';
        $this->enabled = $enabled;

        if ($this->enabled && !file_exists($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log a transaction event
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];

        $filename = $this->logPath . '/payment_' . date('Y-m-d') . '.log';
        $logLine = json_encode($logEntry) . PHP_EOL;

        file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log an info message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log a warning message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log an error message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log a success message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function success(string $message, array $context = []): void
    {
        $this->log(self::SUCCESS, $message, $context);
    }

    /**
     * Log a payment request
     *
     * @param string $provider
     * @param array $data
     * @return void
     */
    public function logPaymentRequest(string $provider, array $data): void
    {
        $this->info("Payment request initiated", [
            'provider' => $provider,
            'amount' => $data['amount'] ?? null,
            'reference' => $data['reference'] ?? null,
        ]);
    }

    /**
     * Log a payment response
     *
     * @param string $provider
     * @param bool $success
     * @param array $data
     * @return void
     */
    public function logPaymentResponse(string $provider, bool $success, array $data): void
    {
        $level = $success ? self::SUCCESS : self::ERROR;
        $message = $success ? "Payment completed successfully" : "Payment failed";

        $this->log($level, $message, [
            'provider' => $provider,
            'data' => $data,
        ]);
    }

    /**
     * Log a transaction verification
     *
     * @param string $provider
     * @param string $transactionId
     * @param string $status
     * @return void
     */
    public function logVerification(string $provider, string $transactionId, string $status): void
    {
        $this->info("Transaction verification", [
            'provider' => $provider,
            'transaction_id' => $transactionId,
            'status' => $status,
        ]);
    }
}
