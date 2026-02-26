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
        $sql = 'SELECT value, recorded_at 
                FROM sensor_data 
                WHERE sensor_id = :sensorId 
                ORDER BY recorded_at DESC 
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':sensorId', $sensorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
