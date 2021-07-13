<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    /**
     * @Route("/lucky/number/{id}")
     */
    public function number($id): Response
    {
        $number = random_int(0, $id);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }

}
