CREATE TABLE notes (
                       id INTEGER PRIMARY KEY AUTOINCREMENT,
                       title VARCHAR(100) NOT NULL,
                       content TEXT NOT NULL,
                       tag VARCHAR(50) NOT NULL,
                       priority VARCHAR(10) NOT NULL CHECK (priority IN ('niski','średni','wysoki')),
                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attachments (
                             id INTEGER PRIMARY KEY AUTOINCREMENT,
                             note_id INTEGER NOT NULL,
                             filename VARCHAR(255) NOT NULL,
                             upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                             FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
);