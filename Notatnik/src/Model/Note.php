<?php

class Note
{
    private $id;
    private $title;
    private $content;
    private $tag;
    private $priority;
    private $createdAt;
    private $attachment; // nazwa pliku (lub null)

    // Gettery i settery
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getContent() { return $this->content; }
    public function getTag() { return $this->tag; }
    public function getPriority() { return $this->priority; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getAttachment() { return $this->attachment; }

    public function setTitle($title) { $this->title = $title; }
    public function setContent($content) { $this->content = $content; }
    public function setTag($tag) { $this->tag = $tag; }
    public function setPriority($priority) { $this->priority = $priority; }
    public function setCreatedAt($date) { $this->createdAt = $date; }
    public function setAttachment($file) { $this->attachment = $file; }

    public function getPreview($length = PREVIEW_LENGTH)
    {
        return mb_strlen($this->content) > $length
            ? mb_substr($this->content, 0, $length) . '…'
            : $this->content;
    }
}