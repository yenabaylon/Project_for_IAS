CREATE TABLE intrusions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    detected_pattern TEXT,
    file_name VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
