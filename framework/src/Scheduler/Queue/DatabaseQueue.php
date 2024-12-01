<?php

declare(strict_types=1);

namespace DJWeb\Framework\Scheduler\Queue;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\Encryption\EncryptionService;
use DJWeb\Framework\Scheduler\Contracts\JobContract;
use DJWeb\Framework\Scheduler\Contracts\QueueContract;

readonly class DatabaseQueue implements QueueContract
{
    public function __construct(private QueryBuilderFacadeContract $builder = new QueryBuilder())
    {
    }
    public function push(JobContract $job): string
    {
        $id = uniqid();
        $payload = new EncryptionService()->encrypt($job);
        $this->builder->insert('jobs')->values([
            'id' => $id,
            'payload' => $payload,
            'available_at' => Carbon::now()->toDateTimeString(),
        ]);
        return $id;
    }

    public function later(\DateTimeInterface $delay, JobContract $job): string
    {
        $id = uniqid();
        $payload = new EncryptionService()->encrypt($job);
        $this->builder->insert('jobs')->values([
            'id' => $id,
            'payload' => $payload,
            'available_at' => $delay->format('Y-m-d H:i:s'),
        ]);
        return $id;
    }

    public function delete(string $id): void
    {
        $this->builder->delete('jobs')->where('id', '=', $id)->delete();
    }

    public function size(): int
    {
        return $this->builder->select('jobs')->select(['count(*) as num'])->first()['num'];
    }

    public function pop(): ?JobContract
    {
        $item = $this->builder
            ->select('jobs')
            ->select()
            ->orderBy('available_at')
            ->first();
        if(!$item) {
            return null;
        }
        $this->builder->delete('jobs')->where('id', '=', $item['id'])->delete();
        return new EncryptionService()->decrypt($item['payload']);
    }
}