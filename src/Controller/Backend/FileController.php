<?php

namespace App\Controller\Backend;

use App\Entity\File;
use App\Form\FileType;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('backend_file_index');
        }

        return $this->render('file/new.html.twig', [
            'file' => $file,
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
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_file_index');
    }
}
