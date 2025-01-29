<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Facades\File;

class CreateEmployeeLog
{
  /**
   * Create the log channel.
   *
   * @return \Monolog\Logger
   */
  public function __invoke()
  {
    // Define your log base path
    $basePath = base_path('laravel_logs/employee/add_employee');  // Custom path

    // Ensure the directory exists or create it if it doesn't
    if (!File::exists($basePath)) {
      File::makeDirectory($basePath, 0755, true);  // Create all parent directories
    }

    // Get current date parts
    $year = date('Y');   // Current year

    // Build the log file path
    $logPath = $basePath . '/' . $year . '/add_employee_data.txt';


    // Ensure the log directories exist
    $directory = dirname($logPath);
    if (!File::exists($directory)) {
      File::makeDirectory($directory, 0755, true);  // Create necessary directories if they don't exist
    }

    // Create the logger instance with the dynamic path
    $logger = new Logger('employee_log');

    // Create a StreamHandler with a custom format
    $streamHandler = new StreamHandler($logPath, Logger::DEBUG);

    // Custom format to remove timestamp and context
    $formatter = new LineFormatter("%message%\n", null, true, true);  // %message% only for the message part
    $streamHandler->setFormatter($formatter);

    // Push the custom handler with the custom formatter to the logger
    $logger->pushHandler($streamHandler);

    return $logger;
  }
}
