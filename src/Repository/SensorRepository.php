<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class SensorRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Return the latest value for a single sensor.
     *
     * @param int $sensorId
     * @return array|null Returns an associative array with keys 'value' and 'recorded_at', or null if no data is found.
     * @throws \PDOException on database errors
     */
    public function getLatest($sensorId): array
    {
        $stmt = $this->pdo->prepare('SELECT value, recorded_at from sensor_data WHERE sensor_id = :sensorId ORDER BY recorded_at DESC LIMIT 1');
        $stmt->bindValue(':sensorId', $sensorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
