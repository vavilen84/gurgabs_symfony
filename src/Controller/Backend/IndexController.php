<?php

namespace App\Controller\Backend;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="backend_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('backend/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }
}