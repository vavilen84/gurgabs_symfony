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

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
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
    public function video(FileRepository $fileRepository): Response
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
    public function audio(FileRepository $fileRepository): Response
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
    public function photo(FileRepository $fileRepository): Response
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
    public function products(FileRepository $fileRepository): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render('frontend/products.html.twig', [
            'products' => $products,
        ]);
    }
}