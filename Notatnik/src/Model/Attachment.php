<?php

class Attachment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function save($noteId, $filename)
    {
        $sql = "INSERT INTO attachments (note_id, filename) VALUES (?, ?)";
        $this->db->query($sql, [$noteId, $filename]);
        return $this->db->lastInsertId();
    }

    public function findByNoteId($noteId)
    {
        $sql = "SELECT * FROM attachments WHERE note_id = ?";
        $stmt = $this->db->query($sql, [$noteId]);
        return $stmt->fetch();
    }

    public function deleteByNoteId($noteId)
    {
        // Fizyczne usunięcie pliku
        $attachment = $this->findByNoteId($noteId);
        if ($attachment) {
            $filePath = UPLOAD_DIR . $attachment['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Usuwamy rekord z bazy (kaskadowo lub ręcznie)
        $sql = "DELETE FROM attachments WHERE note_id = ?";
        $this->db->query($sql, [$noteId]);
    }
}