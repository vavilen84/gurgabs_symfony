<?php

namespace App\Controller\Backend;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/backend/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="backend_event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="backend_event_new", methods={"GET","POST"})
     */
    public function new(Request $request, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('backend_event_index');
        }

        return $this->render('backend/event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_event_show", methods={"GET"})
     */
    public function show(Event $event, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Event $event, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_event_index');
        }

        return $this->render('backend/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_event_index');
    }
}
