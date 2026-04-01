CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS shipments (
    id SERIAL PRIMARY KEY,
    tracking_number VARCHAR(50) NOT NULL UNIQUE,
    sender_name VARCHAR(100) NOT NULL,
    receiver_name VARCHAR(100) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    current_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_by INT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS shipment_status_history (
    id SERIAL PRIMARY KEY,
    shipment_id INT NOT NULL REFERENCES shipments(id) ON DELETE CASCADE,
    status VARCHAR(50) NOT NULL,
    note TEXT,
    updated_by INT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE users TO logistics_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE shipments TO logistics_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE shipment_status_history TO logistics_user;

GRANT USAGE, SELECT ON SEQUENCE users_id_seq TO logistics_user;
GRANT USAGE, SELECT ON SEQUENCE shipments_id_seq TO logistics_user;
GRANT USAGE, SELECT ON SEQUENCE shipment_status_history_id_seq TO logistics_user;