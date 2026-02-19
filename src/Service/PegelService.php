<?php

namespace App\Service;

use App\Repository\SensorRepository;

class PegelService
{
    public function __construct(
        private SensorRepository $sensorRepository
    ) {}


    public function getLatest(): array
    {
        return $this->sensorRepository->getLatest(1);
    }
}
