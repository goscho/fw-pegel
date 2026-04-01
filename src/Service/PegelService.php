<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\SensorRepository;

class PegelService
{
    private static $PEGEL_ID = 1;

    public function __construct(
        private SensorRepository $sensorRepository
    ) {}


    public function getLatest(): array
    {
        return $this->sensorRepository->getLatest(self::$PEGEL_ID);
    }

    public function addValue(float $value, string $recordedAt): int
    {
        return $this->sensorRepository->save(self::$PEGEL_ID, $value, $recordedAt);
    }

    public function getHistory(string $from, string $to): array
    {
        return $this->sensorRepository->getByTimeframe(self::$PEGEL_ID, $from, $to);
    }
}
