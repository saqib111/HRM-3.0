<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\File;

use Monolog\Formatter\LineFormatter;
class TeamLog
{
  /**
   * Create the log channel.
   *
   * @return \Monolog\Logger
   */
  public function __invoke()
  {
    $basePath = base_path('laravel_logs/TeamInfo');
    if (!File::exists($basePath)) {
      File::makeDirectory($basePath, 0755, true);
    }

    // Define the path of the log file
    $logPath = $basePath . '/teams_data.txt'; // Directly creating update_employees.txt

    $directory = dirname($logPath);
    if (!File::exists($directory)) {
      File::makeDirectory($directory, 0755, true);
    }

    // Create a new Monolog instance
    $logger = new Logger('team_log');

    // Set up the StreamHandler to handle log writing to the file
    $handler = new StreamHandler($logPath, Logger::DEBUG);

    // Set a custom format that excludes the timestamp and log level
    // %message will be the log content
    $formatter = new LineFormatter("%message%\n", null, true, true); // Format only the message
    $handler->setFormatter($formatter);

    // Push the handler to the logger
    $logger->pushHandler($handler);

    // Return the configured logger instance
    return $logger;
  }
}
