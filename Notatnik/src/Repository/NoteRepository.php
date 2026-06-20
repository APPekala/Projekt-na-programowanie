<?php

class NoteRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll($filters = [], $sort = 'created_at_desc')
    {
        $sql = "SELECT n.*, a.filename as attachment
                FROM notes n
                LEFT JOIN attachments a ON n.id = a.note_id
                WHERE 1=1";
        $params = [];

        // Wyszukiwanie
        if (!empty($filters['search'])) {
            $sql .= " AND (n.title LIKE ? OR n.content LIKE ?)";
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        // Filtrowanie po tagu
        if (!empty($filters['tag'])) {
            $sql .= " AND n.tag = ?";
            $params[] = $filters['tag'];
        }

        // Filtrowanie po priorytecie
        if (!empty($filters['priority'])) {
            $sql .= " AND n.priority = ?";
            $params[] = $filters['priority'];
        }

        // Sortowanie
        switch ($sort) {
            case 'created_at_asc':  $sql .= " ORDER BY n.created_at ASC"; break;
            case 'title_asc':       $sql .= " ORDER BY n.title ASC"; break;
            case 'title_desc':      $sql .= " ORDER BY n.title DESC"; break;
            case 'priority_asc':    $sql .= " ORDER BY n.priority ASC"; break;
            case 'priority_desc':   $sql .= " ORDER BY n.priority DESC"; break;
            default:                $sql .= " ORDER BY n.created_at DESC"; break;
        }

        $stmt = $this->db->query($sql, $params);
        $rows = $stmt->fetchAll();

        $notes = [];
        foreach ($rows as $row) {
            $note = new Note();
            $note->setId($row['id']);
            $note->setTitle($row['title']);
            $note->setContent($row['content']);
            $note->setTag($row['tag']);
            $note->setPriority($row['priority']);
            $note->setCreatedAt($row['created_at']);
            $note->setAttachment($row['attachment'] ?? null);
            $notes[] = $note;
        }
        return $notes;
    }

    public function findById($id)
    {
        $sql = "SELECT n.*, a.filename as attachment
                FROM notes n
                LEFT JOIN attachments a ON n.id = a.note_id
                WHERE n.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $note = new Note();
        $note->setId($row['id']);
        $note->setTitle($row['title']);
        $note->setContent($row['content']);
        $note->setTag($row['tag']);
        $note->setPriority($row['priority']);
        $note->setCreatedAt($row['created_at']);
        $note->setAttachment($row['attachment'] ?? null);
        return $note;
    }

    public function save(Note $note, $file = null)
    {
        $db = $this->db->getPdo();
        $db->beginTransaction();

        try {
            if ($note->getId()) {
                // UPDATE
                $sql = "UPDATE notes SET title = ?, content = ?, tag = ?, priority = ? WHERE id = ?";
                $this->db->query($sql, [
                    $note->getTitle(),
                    $note->getContent(),
                    $note->getTag(),
                    $note->getPriority(),
                    $note->getId()
                ]);
                $noteId = $note->getId();
            } else {
                // INSERT
                $sql = "INSERT INTO notes (title, content, tag, priority) VALUES (?, ?, ?, ?)";
                $this->db->query($sql, [
                    $note->getTitle(),
                    $note->getContent(),
                    $note->getTag(),
                    $note->getPriority()
                ]);
                $noteId = $this->db->lastInsertId();
            }

            // Obsługa załącznika (jeśli przesłano nowy)
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                // Usuń stary załącznik (jeśli edycja)
                if ($note->getId()) {
                    $attachmentModel = new Attachment();
                    $attachmentModel->deleteByNoteId($note->getId());
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = uniqid() . '.' . $ext;
                $dest = UPLOAD_DIR . $newName;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $attachmentModel = new Attachment();
                    $attachmentModel->save($noteId, $newName);
                }
            }

            $db->commit();
            return $noteId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        $db = $this->db->getPdo();
        $db->beginTransaction();
        try {
            // Usuń załącznik fizycznie
            $attachmentModel = new Attachment();
            $attachmentModel->deleteByNoteId($id);

            // Usuń notatkę
            $sql = "DELETE FROM notes WHERE id = ?";
            $this->db->query($sql, [$id]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function getAllTags()
    {
        $sql = "SELECT DISTINCT tag FROM notes ORDER BY tag";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getStats()
    {
        $stats = [];
        // Liczba notatek
        $sql = "SELECT COUNT(*) as total FROM notes";
        $stmt = $this->db->query($sql);
        $stats['total'] = $stmt->fetch()['total'];

        // Priorytety
        $sql = "SELECT priority, COUNT(*) as count FROM notes GROUP BY priority";
        $stmt = $this->db->query($sql);
        $stats['priorities'] = [];
        while ($row = $stmt->fetch()) {
            $stats['priorities'][$row['priority']] = $row['count'];
        }

        // Tagi
        $sql = "SELECT tag, COUNT(*) as count FROM notes GROUP BY tag ORDER BY count DESC";
        $stmt = $this->db->query($sql);
        $stats['tags'] = [];
        while ($row = $stmt->fetch()) {
            $stats['tags'][$row['tag']] = $row['count'];
        }

        return $stats;
    }
}