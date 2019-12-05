<?php

namespace App\Controller\Frontend;

use App\Entity\Event;
use App\Entity\File;
use App\Entity\Product;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\File as FileEnum;
use App\Enum\Product as ProductEnum;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Container\ProductInCart;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('frontend/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/video", name="video", methods={"GET"})
     */
    public function video(): Response
    {
        $fileRepository = $this->getDoctrine()->getRepository(File::class);
        $files = $fileRepository->findBy(['type' => FileEnum::VIDEO_TYPE], ['id' => 'DESC']);

        return $this->render('frontend/video.html.twig', [
            'files' => $files,
        ]);
    }

    /**
     * @Route("/audio", name="audio", methods={"GET"})
     */
    public function audio(): Response
    {
        $fileRepository = $this->getDoctrine()->getRepository(File::class);
        $files = $fileRepository->findBy(['type' => FileEnum::MUSIC_TYPE], ['id' => 'DESC']);

        return $this->render('frontend/audio.html.twig', [
            'files' => $files,
        ]);
    }

    /**
     * @Route("/photo", name="photo", methods={"GET"})
     */
    public function photo(): Response
    {
        $fileRepository = $this->getDoctrine()->getRepository(File::class);
        $files = $fileRepository->findBy(['type' => FileEnum::PHOTO_TYPE], ['id' => 'DESC']);

        return $this->render('frontend/photo.html.twig', [
            'files' => $files,
        ]);
    }

    /**
     * @Route("/products", name="products", methods={"GET"})
     */
    public function products(): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render('frontend/products.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/cart", name="cart", methods={"GET"})
     */
    public function cart(): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        $cart = $this->session->get('cart');
        $products = [];
        if (!empty($cart)) {
            foreach ($cart as $productInCart) {
                if ($productInCart instanceof ProductInCart) {
                    $product = $productRepository->findOneBy(['id' => $productInCart->getProduct()->getId()]);
                    if ($product instanceof Product){
                        $productInCart->setProduct($product);
                        $products[] = $productInCart;
                    }
                }
            }
        }

        return $this->render('frontend/cart.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/add-to-cart/{id}", name="add_to_cart", methods={"GET"})
     */
    public function addToCart(Product $product): Response
    {
        $cart = $this->session->get('cart');
        if (empty($cart)) {
            $cart = [];
        }
        if (isset($cart[$product->getId()])) {
            $container = $cart[$product->getId()];
            if ($container instanceof ProductInCart) {
                $container->setQuantity($container->getQuantity() + 1);
            }
        } else {
            $container = new ProductInCart();
            $container->setProduct($product);
            $container->setQuantity(1);
        }
        $cart[$product->getId()] = $container;
        $cart = array_unique($cart);
        $this->session->set('cart', $cart);

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/remove-from-cart/{id}", name="remove_from_cart", methods={"GET"})
     */
    public function removeFromCart(Product $product): Response
    {
        $cart = $this->session->get('cart');
        if (empty($cart) || empty($cart[$product->getId()])) {
            return $this->redirectToRoute('cart');
        }
        $container = $cart[$product->getId()];
        if ($container instanceof ProductInCart) {
            $currentQuantity = $container->getQuantity();
            if ($currentQuantity < 2) {
                unset($cart[$product->getId()]);
            } else {
                $container->setQuantity($container->getQuantity() - 1);
            }
        }
        $this->session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }
}