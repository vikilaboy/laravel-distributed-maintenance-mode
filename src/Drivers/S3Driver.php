<?php

declare(strict_types=1);

namespace Vikilaboy\DistributedMaintenanceMode\Drivers;

use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Filesystem\FilesystemManager;

class S3Driver implements MaintenanceMode
{
    private const FILENAME = 'maintenance-mode.json';

    public function __construct(private readonly FilesystemManager $manager, private readonly string $disk)
    {
    }

    public function activate(array $payload): void
    {
        $this->manager->disk($this->disk)->put(self::FILENAME, json_encode($payload));
    }

    public function deactivate(): void
    {
        $this->manager->disk($this->disk)->delete(self::FILENAME);
    }

    public function active(): bool
    {
        return $this->manager->disk($this->disk)->exists(self::FILENAME);
    }

    public function data(): array
    {
        return json_decode($this->manager->disk($this->disk)->get(self::FILENAME), true);
    }
}
