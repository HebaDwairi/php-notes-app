#users can comment whether logged in or not so the user_id is optional and if comment is by a guest its given a guest name
#only logged in users can like a post

CREATE TABLE comments(
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  note_id INT NOT NULL,
  content TEXT NOT NULL,
  is_guest BOOLEAN NOT NULL DEFAULT FALSE,
  guest_name VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending', 'approved'),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
);

CREATE TABLE likes(
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  note_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
);

ALTER TABLE comments MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending';