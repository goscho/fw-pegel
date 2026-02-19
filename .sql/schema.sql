CREATE TABLE sensor_metadata (
    sensor_id TINYINT UNSIGNED PRIMARY KEY,
    name VARCHAR(100),
    location VARCHAR(100),
    unit_short VARCHAR(20),
    unit_long VARCHAR(50)
);

CREATE TABLE sensor_data (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    sensor_id TINYINT UNSIGNED NOT NULL REFERENCES sensor_metadata(sensor_id),
    value DOUBLE,
    recorded_at DATETIME,
    INDEX(sensor_id, recorded_at)
);

INSERT INTO `sensor_metadata` (`sensor_id`, `name`, `location`, `unit_short`, `unit_long`)
VALUES ('1', 'Murrpegel Feuerwehrhaus Murrhardt', NULL, 'm', 'Meter'),
('2', 'Niederschlag beim Feuerwehrhaus Murrhardt', NULL, 'l', 'Liter');