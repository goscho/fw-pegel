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

    /**
     * Return sensor values within a specified timeframe.
     *
     * @param int $sensorId
     * @param string $from Start datetime in 'Y-m-d H:i:s' format
     * @param string $to End datetime in 'Y-m-d H:i:s' format
     * @param int $limit Maximum number of results to return
     * @return array Returns an array of associative arrays with keys 'value' and 'recorded_at'
     * @throws \PDOException on database errors
     */
    public function getByTimeframe(int $sensorId, string $from, string $to, int $limit = 2500): array
    {
        $sql = 'SELECT value, recorded_at 
                FROM sensor_data 
                WHERE sensor_id = :sensorId 
                AND recorded_at BETWEEN :from AND :to
                ORDER BY recorded_at ASC 
                LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':sensorId', $sensorId, PDO::PARAM_INT);
        $stmt->bindValue(':from', $from, PDO::PARAM_STR);
        $stmt->bindValue(':to', $to, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Save a new sensor reading to the database.
     *
     * @param int $sensorId
     * @param float $value
     * @param string $recordedAt
     * @return int Returns the ID of the inserted record
     * @throws \PDOException on database errors
     */
    public function save(int $sensorId, float $value, string $recordedAt): int
    {
        $sql = 'INSERT INTO sensor_data (sensor_id, value, recorded_at) 
                VALUES (:sensorId, :value, :recordedAt)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':sensorId', $sensorId, PDO::PARAM_INT);
        $stmt->bindValue(':value', $value, PDO::PARAM_STR);
        $stmt->bindValue(':recordedAt', $recordedAt, PDO::PARAM_STR);
        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }
}
