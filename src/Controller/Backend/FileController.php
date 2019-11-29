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

/**
 * @Route("/backend/file")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/", name="backend_file_index", methods={"GET"})
     */
    public function index(FileRepository $fileRepository): Response
    {
        return $this->render('backend/file/index.html.twig', [
            'files' => $fileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="backend_file_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
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

            return $this->redirectToRoute('backend_file_index');
        }

        return $this->render('backend/file/new.html.twig', [
            'file' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="backend_file_show", methods={"GET"})
     */
    public function show(File $file): Response
    {
        return $this->render('backend/file/show.html.twig', [
            'file' => $file,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_file_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, File $file): Response
    {
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_file_index');
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

        return $this->redirectToRoute('backend_file_index');
    }
}
