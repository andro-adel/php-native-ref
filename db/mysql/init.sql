CREATE TABLE
IF NOT EXISTS users
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(100) NOT NULL,
  email VARCHAR
(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users
  (name, email)
VALUES
  ('Alice', 'alice@example.com'),
  ('Bob', 'bob@example.com');
