<?php

class Router
{
    public function dispatch()
    {
        $action = $_GET['action'] ?? 'list';
        $controller = new NoteController();

        switch ($action) {
            case 'create':
                $controller->formAction();
                break;
            case 'edit':
                $id = (int)($_GET['id'] ?? 0);
                $controller->formAction($id);
                break;
            case 'save':
                $controller->saveAction();
                break;
            case 'delete':
                $controller->deleteAction();
                break;
            case 'list':
            default:
                $controller->listAction();
                break;
        }
    }
}