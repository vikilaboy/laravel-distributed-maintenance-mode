<?php

declare(strict_types=1);

namespace Despark\DistributedMaintenanceMode\Drivers;

use Illuminate\Contracts\Foundation\MaintenanceMode;
use Predis\Client;

class RedisDriver implements MaintenanceMode
{
    private const CACHE_KEY = 'maintenance-mode';

    public function __construct(private readonly Client $redisClient)
    {
    }

    /**
     * @throws \RedisException
     */
    public function activate(array $payload): void
    {
        $this->redisClient->set(self::CACHE_KEY, json_encode($payload));
    }

    /**
     * @throws \RedisException
     */
    public function deactivate(): void
    {
        $this->redisClient->del(self::CACHE_KEY);
    }

    /**
     * @throws \RedisException
     */
    public function active(): bool
    {
        return !is_null($this->getData());
    }

    /**
     * @throws \RedisException
     */
    public function data(): array
    {
        $data = $this->getData();

        if (is_null($data)) {
            return [];
        }

        return json_decode($data, true);
    }

    /**
     * @throws \RedisException
     */
    private function getData(): ?string
    {
        return $this->redisClient->get(self::CACHE_KEY);
    }
}
