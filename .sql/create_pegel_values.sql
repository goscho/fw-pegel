TRUNCATE TABLE sensor_data;

-- Drop the procedure if it already exists to avoid errors on re-creation
DROP PROCEDURE IF EXISTS PopulateSensorData;

-- Change the delimiter to allow for semicolons within the procedure
DELIMITER $$

-- Create the stored procedure
CREATE PROCEDURE PopulateSensorData()
BEGIN
    -- Declare variables for the loop
    DECLARE v_current_timestamp DATETIME;
    DECLARE v_end_timestamp DATETIME;
    DECLARE v_random_value DOUBLE;

    -- Set the start and end times for data generation
    SET v_current_timestamp = '2026-01-01 00:00:00';
    SET v_end_timestamp = NOW(); -- Or a specific end date like '2026-12-31 23:59:59'

    -- Start a transaction for faster inserts
    START TRANSACTION;

    -- Loop from the start time to the end time
    WHILE v_current_timestamp <= v_end_timestamp DO
        -- Generate a random value in the range [0.1, 4.0]
        SET v_random_value = ROUND(RAND() * (4.0 - 0.1) + 0.1, 2);

        -- Insert the new data point
        INSERT INTO sensor_data (sensor_id, value, recorded_at)
        VALUES (1, v_random_value, v_current_timestamp);

        -- Increment the timestamp by 5 minutes for the next iteration
        SET v_current_timestamp = DATE_ADD(v_current_timestamp, INTERVAL 5 MINUTE);
    END WHILE;

    -- Commit the transaction to save all the inserted rows
    COMMIT;

END$$

-- Restore the default delimiter
DELIMITER ;

call PopulateSensorData();

DROP PROCEDURE PopulateSensorData;