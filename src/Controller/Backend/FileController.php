<?php

namespace App\Controller\Backend;

use App\Entity\File;
use App\Form\FileType;
use App\Repository\FileRepository;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Helpers\Common;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Entity\Product;

/**
 * @Route("/backend/file")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/", name="backend_file_index", methods={"GET"})
     */
    public function index(FileRepository $fileRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/file/index.html.twig', [
            'files' => $fileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/audio", name="backend_audio_index", methods={"GET"})
     */
    public function audio(FileRepository $fileRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/file/index.html.twig', [
            'files' => $fileRepository->findBy(['type' => \App\Enum\File::MUSIC_TYPE]),
        ]);
    }

    /**
     * @Route("/video", name="backend_video_index", methods={"GET"})
     */
    public function video(FileRepository $fileRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/file/index.html.twig', [
            'files' => $fileRepository->findBy(['type' => \App\Enum\File::VIDEO_TYPE]),
        ]);
    }

    /**
     * @Route("/photo", name="backend_photo_index", methods={"GET"})
     */
    public function photo(FileRepository $fileRepository, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/file/index.html.twig', [
            'files' => $fileRepository->findBy(['type' => \App\Enum\File::PHOTO_TYPE]),
        ]);
    }

    /**
     * @Route("/new", name="backend_file_new", methods={"GET","POST"})
     */
    public function new(Request $request, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        $model = new File();
        $form = $this->createForm(FileType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form['file']->getData();
            $ext = $file->guessClientExtension();
            $fileName = Common::getUniqueString() . '.' . $ext;
            try {
                $file->move(
                    $this->getParameter('media_folder'),
                    $fileName
                );
            } catch (FileException $e) {
                throw new \Exception($e);
            }
            $model->setFile($fileName);
            $model->setExt($ext);
            $model->setOriginalName($file->getClientOriginalName());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($model);
            $entityManager->flush();

            return $this->redirectToRoute('backend_index');
        }

        return $this->render('backend/file/new.html.twig', [
            'file' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_file_show", methods={"GET"})
     */
    public function show(File $file, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        return $this->render('backend/file/show.html.twig', [
            'file' => $file,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_file_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, File $file, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem("Files", $this->get("router")->generate("backend_file_index"));

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_index');
        }

        return $this->render('backend/file/edit.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_file_delete", methods={"DELETE"})
     */
    public function delete(Request $request, File $file): Response
    {
        if ($this->isCsrfTokenValid('delete' . $file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_index');
    }


    /**
     * @Route("/product-image-delete/{id}", name="backend_product_image_delete", methods={"DELETE"})
     */
    public function productImageDelete(Request $request, File $file): Response
    {
        $product = $file->getProduct();
        if ($this->isCsrfTokenValid('delete' . $file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_product_edit', ['id' => $product->getId()]);
    }

    /**
     * @Route("/product/{id}/add-image", name="backend_file_add_product_image", methods={"GET","POST"})
     */
    public function addProductImage(Request $request, Product $product, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Backend", $this->get("router")->generate("backend_index"));
        $breadcrumbs->addItem(
            "Product",
            $this->generateUrl("backend_product_edit", ['id' => $product->getId()])
        );
        $breadcrumbs->addItem(
            "Files",
            $this->generateUrl("backend_file_add_product_image", ['id' => $product->getId()])
        );

        $model = new File();

        $form = $this->createForm(FileType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form['file']->getData();
            $ext = $file->guessClientExtension();
            $fileName = Common::getUniqueString() . '.' . $ext;
            try {
                $file->move(
                    $this->getParameter('media_folder'),
                    $fileName
                );
            } catch (FileException $e) {
                throw new \Exception($e);
            }
            $model->setType(\App\Enum\File::PRODUCT_IMAGE_TYPE);
            $model->setProduct($product);
            $model->setFile($fileName);
            $model->setExt($ext);
            $model->setOriginalName($file->getClientOriginalName());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($model);
            $entityManager->flush();

            return $this->redirectToRoute('backend_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('backend/file/add-product-image.html.twig', [
            'file' => $model,
            'form' => $form->createView(),
        ]);
    }
}
