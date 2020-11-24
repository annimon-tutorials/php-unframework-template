<?php

namespace Controllers;

class AppController extends AbstractController {

    public function index() {
        $user = $this->db()
            ->query('SELECT id, name FROM users WHERE id = 1')
            ->fetch();
        $this->render('index.twig', ['user' => $user]);
    }

    public function about() {
        $this->render('about.twig');
    }
}