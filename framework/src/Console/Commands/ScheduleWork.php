<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;
use DJWeb\Framework\Scheduler\QueueFactory;
use DJWeb\Framework\Scheduler\Schedule;
use DJWeb\Framework\Scheduler\Workers\ScheduleWorker;

#[AsCommand('schedule:work')]
class ScheduleWork extends Command
{
    public function run(): int
    {
        $queue = QueueFactory::make();
        $schedule = $this->configureSchedule();

        $this->getOutput()->info('Schedule worker started.');

        new ScheduleWorker($schedule, $queue)->work();

        return 0;
    }

    private function configureSchedule(): Schedule
    {
        $schedule = new Schedule();

        $app = Application::getInstance();
        $configPath = $app->base_path . '/config/schedule.php';

        if (!file_exists($configPath)) {
            throw new \RuntimeException('Schedule configuration file not found');
        }

        $config = require $configPath;
        $schedule->addFromConfig($config);

        return $schedule;
    }
}