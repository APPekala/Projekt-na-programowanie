<?php

class Validator
{
    private $errors = [];

    public function validateNote($data, $isEdit = false)
    {
        $this->errors = [];

        $title = trim($data['title'] ?? '');
        if (mb_strlen($title) < TITLE_MIN || mb_strlen($title) > TITLE_MAX) {
            $this->errors['title'] = "Tytuł musi mieć od " . TITLE_MIN . " do " . TITLE_MAX . " znaków.";
        }

        $content = trim($data['content'] ?? '');
        if (empty($content)) {
            $this->errors['content'] = "Treść nie może być pusta.";
        }

        $tag = trim($data['tag'] ?? '');
        if (empty($tag)) {
            $this->errors['tag'] = "Tag jest wymagany.";
        } elseif (!preg_match('/^[\p{L}\p{N}\-_\s]+$/u', $tag)) {
            $this->errors['tag'] = "Tag zawiera niedozwolone znaki (dozwolone: litery, cyfry, -, _, spacja).";
        } elseif (mb_strlen($tag) > TAG_MAX) {
            $this->errors['tag'] = "Tag może mieć maksymalnie " . TAG_MAX . " znaków.";
        }

        $priority = $data['priority'] ?? '';
        if (!in_array($priority, PRIORITIES)) {
            $this->errors['priority'] = "Nieprawidłowy priorytet.";
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
