<?php

namespace App\Controller\Backend;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="backend_order_index", methods={"GET"})
     */
    public function index(OrderRepository $orderRepository): Response
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $orders = $qb
            ->select('o')
            ->from('order', 'o')
            ->orderBy('o.id', 'DESC');

        return $this->render('backend/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_order_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
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
