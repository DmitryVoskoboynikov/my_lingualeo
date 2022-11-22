<?php

// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    // ...

    public function base(): Response
    {
        // the template path is the relative file path from `templates/`
        return $this->render('base.html.twig', []);
    }
}
