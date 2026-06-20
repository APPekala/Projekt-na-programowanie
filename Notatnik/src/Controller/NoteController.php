<?php

class NoteController
{
    private $repository;
    private $validator;

    public function __construct()
    {
        $this->repository = new NoteRepository();
        $this->validator = new Validator();
    }

    // Wyświetlenie listy z filtrami
    public function listAction()
    {
        $search = $_GET['search'] ?? '';
        $tagFilter = $_GET['tag_filter'] ?? '';
        $priorityFilter = $_GET['priority_filter'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at_desc';

        $filters = [
            'search' => $search,
            'tag' => $tagFilter,
            'priority' => $priorityFilter
        ];

        $notes = $this->repository->findAll($filters, $sort);
        $allTags = $this->repository->getAllTags();
        $stats = $this->repository->getStats();

        // Przygotowanie danych do widoku
        $totalNotes = $stats['total'];
        $statsByPriority = $stats['priorities'];
        $statsByTag = $stats['tags'];

        include __DIR__ . '/../../templates/layout.php';
    }

    // Formularz dodawania/edycji
    public function formAction($id = null)
    {
        $note = null;
        if ($id) {
            $note = $this->repository->findById($id);
            if (!$note) {
                $this->setFlash('Notatka nie istnieje.', 'error');
                header('Location: ' . BASE_URL . '?action=list');
                exit;
            }
        }

        $errors = [];
        $oldData = [];

        // Jeśli przekazano dane z flash (po błędzie walidacji)
        if (isset($_SESSION['old_data'])) {
            $oldData = $_SESSION['old_data'];
            $errors = $_SESSION['errors'] ?? [];
            unset($_SESSION['old_data'], $_SESSION['errors']);
        } elseif ($note) {
            $oldData = [
                'title' => $note->getTitle(),
                'content' => $note->getContent(),
                'tag' => $note->getTag(),
                'priority' => $note->getPriority()
            ];
        }

        include __DIR__ . '/../../templates/form.php';
    }

    // Zapis (dodawanie / edycja)
    public function saveAction()
    {
        $data = $_POST;
        $file = $_FILES['attachment'] ?? null;

        // Walidacja
        $isEdit = isset($data['id']) && $data['id'] > 0;
        if (!$this->validator->validateNote($data, $isEdit)) {
            $_SESSION['old_data'] = $data;
            $_SESSION['errors'] = $this->validator->getErrors();
            $redirect = $isEdit ? 'edit&id=' . $data['id'] : 'create';
            header('Location: ' . BASE_URL . '?action=' . $redirect);
            exit;
        }

        // Tworzenie obiektu Note
        $note = new Note();
        if ($isEdit) {
            $note->setId((int)$data['id']);
        }
        $note->setTitle(trim($data['title']));
        $note->setContent(trim($data['content']));
        $note->setTag(trim($data['tag']));
        $note->setPriority($data['priority']);

        // Zapis (z załącznikiem)
        try {
            $this->repository->save($note, $file);
            $this->setFlash('Notatka została zapisana.', 'success');
        } catch (Exception $e) {
            $this->setFlash('Błąd podczas zapisu: ' . $e->getMessage(), 'error');
        }

        header('Location: ' . BASE_URL . '?action=list');
        exit;
    }

    // Usuwanie
    public function deleteAction()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            try {
                $this->repository->delete($id);
                $this->setFlash('Notatka usunięta.', 'success');
            } catch (Exception $e) {
                $this->setFlash('Błąd podczas usuwania: ' . $e->getMessage(), 'error');
            }
        }
        header('Location: ' . BASE_URL . '?action=list');
        exit;
    }

    private function setFlash($message, $type = 'info')
    {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }

    public function getFlash()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}