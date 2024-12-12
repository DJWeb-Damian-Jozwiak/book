<?php

declare(strict_types=1);

namespace DJWeb\Framework\Scheduler\Workers;

use DJWeb\Framework\Scheduler\Contracts\QueueContract;

readonly class QueueWorker
{
    public function __construct(private QueueContract $queue, private int $iterations = 0)
    {
    }

    public function work(): void
    {
        $iterations = 0;
        while (true) {
            $job = $this->queue->pop();

            if(! $this->shouldContinue($iterations)) {
                break;
            }
            $iterations++;
            if ($job === null) {
                sleep(1);
                continue;
            }

            try {
                $job->handle();
            } catch (\Throwable $e) {
                $job->handleException($e);
            }
        }
    }

    private function shouldContinue(int $iterations): bool
    {
        return $this->iterations === 0 || $this->iterations > $iterations;
    }
}
