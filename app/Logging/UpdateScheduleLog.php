<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\File;
use Monolog\Formatter\LineFormatter;

class UpdateScheduleLog
{
  /**
   * Create the log channel.
   *
   * @return \Monolog\Logger
   */
  public function __invoke($shift_in)
  {
    // If the shiftIn date is provided, use it. If not, fallback to the current date.
    if ($shift_in) {
      // Parse the shiftIn date and get year, month, day parts
      $year = date('Y', strtotime($shift_in));   // Year part from shiftIn
      $month = date('M', strtotime($shift_in));  // Short month name (e.g., "Jan", "Feb", "Mar")
      $day = date('d', strtotime($shift_in));    // Day part from shiftIn
    } else {
      // Fallback to current date if shiftIn is not provided
      $year = date('Y');
      $month = date('M');
      $day = date('d');
    }

    // Base path for logs
    $basePath = base_path('laravel_logs/UpdateSchedule');

    // Create the base directory if it doesn't exist
    if (!File::exists($basePath)) {
      File::makeDirectory($basePath, 0755, true);
    }

    // Build the log file path using shiftIn's year, month, and day
    $logPath = $basePath . '/' . $year . '/' . $month . '/' . $day . '/update_schedule_data.txt';

    // Create the directory structure for the log file if it doesn't exist
    $directory = dirname($logPath);
    if (!File::exists($directory)) {
      File::makeDirectory($directory, 0755, true);
    }

    // Create a new Monolog instance
    $logger = new Logger('update_schedule_log');

    // Set up the StreamHandler to handle log writing to the file
    $handler = new StreamHandler($logPath, Logger::DEBUG);

    // Set a custom format that excludes the timestamp and log level
    $formatter = new LineFormatter("%message%\n", null, true, true); // Format only the message
    $handler->setFormatter($formatter);

    // Push the handler to the logger
    $logger->pushHandler($handler);

    // Return the configured logger instance
    return $logger;
  }
}
