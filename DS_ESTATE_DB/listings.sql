CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    area VARCHAR(255) NOT NULL,
    rooms INT NOT NULL,
    price_per_night INT NOT NULL,
    photo VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);