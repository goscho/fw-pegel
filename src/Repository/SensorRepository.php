<?php

namespace App\Repository;

use PDO;

class SensorRepository
{
    public function __construct(private PDO $pdo) {}

    public function getLatest($sensorId): array
    {
        $stmt = $this->pdo->prepare('SELECT value, recorded_at from sensor_data WHERE sensor_id = :sensorId ORDER BY recorded_at DESC LIMIT 1');
        $stmt->bindValue(':sensorId', $sensorId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch();
        return ["data" => $data];
    }
}
