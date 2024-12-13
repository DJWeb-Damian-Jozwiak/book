<?php

declare(strict_types=1);

namespace DJWeb\Framework\Scheduler\Queue;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Scheduler\Contracts\JobContract;
use DJWeb\Framework\Scheduler\Contracts\QueueContract;
use Redis;

class RedisQueue implements QueueContract
{
    private string $prefix = 'queue:';

    public function __construct(private Redis $redis = new Redis())
    {
        $this->redis->connect(
            Config::get('redis.host'),
            Config::get('redis.port')
        );
    }

    public function withRedis(Redis $redis): static
    {
        $this->redis = $redis;
        return $this;
    }
    public function push(JobContract $job): string
    {
        $id = uniqid();
        $this->redis->zAdd(
            $this->prefix . 'delayed',
            time(),
            json_encode([
                'id' => $id,
                'job' => serialize($job),
            ])
        );
        return $id;
    }

    public function later(\DateTimeInterface $delay, JobContract $job): string
    {
        $id = uniqid();
        $this->redis->zAdd(
            $this->prefix . 'delayed',
            $delay->getTimestamp(),
            json_encode([
                'id' => $id,
                'job' => serialize($job),
            ])
        );
        return $id;
    }

    public function delete(string $id): void
    {
        $this->redis->zRemRangeByScore(
            $this->prefix . 'delayed',
            '0',
            time().''
        );
        $this->redis->zRem($this->prefix . 'delayed', $id);
    }

    public function size(): int
    {
        return $this->redis->zCount(
            $this->prefix . 'delayed',
            '0',
            time().''
        );
    }

    public function pop(): ?JobContract
    {
        $jobs = $this->redis->zRangeByScore(
            $this->prefix . 'delayed',
            '0',
            time().'',
            ['limit' => [0, 1]]
        );

        if (! $jobs) {
            return null;
        }

        $jobData = json_decode($jobs[0], true);
        $this->redis->zRem($this->prefix . 'delayed', $jobs[0]);

        return unserialize($jobData['job']);
    }
}
