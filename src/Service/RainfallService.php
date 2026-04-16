<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\SensorRepository;

class RainfallService
{
    private static $RAINFALL_ID = 2;

    public function __construct(
        private SensorRepository $sensorRepository
    ) {}


    public function getLatest(): array
    {
        return $this->sensorRepository->getLatest(self::$RAINFALL_ID);
    }

    public function addValue(float $value, string $recordedAt): int
    {
        return $this->sensorRepository->save(self::$RAINFALL_ID, $value, $recordedAt);
    }

    public function getHistory(string $from, string $to): array
    {
        return $this->sensorRepository->getByTimeframe(self::$RAINFALL_ID, $from, $to);
    }
}
