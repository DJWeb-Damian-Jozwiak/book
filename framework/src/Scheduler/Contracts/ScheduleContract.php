<?php

declare(strict_types=1);

namespace DJWeb\Framework\Scheduler\Contracts;

use DJWeb\Framework\Scheduler\CronSchedule;

interface ScheduleContract
{
    public function cron(CronSchedule|string $expression, JobContract $job): void;
    public function everyNMinutes(int $minutes, JobContract $job): void;
}
