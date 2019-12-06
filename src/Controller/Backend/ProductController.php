<?php

namespace App\Controller\Backend;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/backend/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="backend_product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("backend_product_index"));

        return $this->render('backend/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="backend_product_new", methods={"GET","POST"})
     */
    public function new(Request $request, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("backend_product_index"));

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('backend_product_index');
        }

        return $this->render('backend/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_product_show", methods={"GET"})
     */
    public function show(Product $product, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("backend_product_index"));

        return $this->render('backend/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("backend_product_index"));

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_product_index');
        }

        return $this->render('backend/product/edit.html.twig', [
            'product' => $product,
            'images' => $product->getImages(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_product_index');
    }
}
