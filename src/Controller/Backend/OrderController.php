<?php

namespace App\Controller\Backend;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/backend/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="backend_order_index", methods={"GET"})
     */
    public function index(OrderRepository $orderRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Orders", $this->get("router")->generate("backend_order_index"));

        return $this->render('backend/order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_order_show", methods={"GET"})
     */
    public function show(Order $order, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Orders", $this->get("router")->generate("backend_order_index"));

        return $this->render('backend/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_order_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Order $order): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_order_index');
    }
}
