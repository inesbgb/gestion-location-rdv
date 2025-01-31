<?php

namespace App\Controller;

use App\Entity\ElementAdmin;
use App\Form\ElementAdminType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ElementAdminRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/element/admin')]
class ElementAdminController extends AbstractController
{
    #[Route('/', name: 'app_element_admin_index', methods: ['GET'])]
    public function index(ElementAdminRepository $elementAdminRepository): Response
    {
        return $this->render('element_admin/index.html.twig', [
            'element_admins' => $elementAdminRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_element_admin_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    // {
    //     $elementAdmin = new ElementAdmin();
    //     $form = $this->createForm(ElementAdminType::class, $elementAdmin);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $uploadFields = ['video', 'carouselImage1', 'carouselImage2', 'carouselImage3', 'imageHistoire'];

    //         foreach ($uploadFields as $field) {
    //             $file = $form->get($field)->getData();
    //             if ($file) {
    //                 $fileName = $fileUploader->upload($file);
    //                 $setter = 'set'.ucfirst($field);
    //                 $elementAdmin->$setter($fileName);
    //             }
    //         }

    //         $entityManager->persist($elementAdmin);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_element_admin_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('element_admin/new.html.twig', [
    //         'element_admin' => $elementAdmin,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_element_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ElementAdmin $elementAdmin, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ElementAdminType::class, $elementAdmin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFields = ['carouselImage1', 'carouselImage2', 'carouselImage3'];

            foreach ($uploadFields as $field) {
                $produit = $form->get($field)->getData();
                if ($produit) {
                    $setter = 'set'.ucfirst($field);
                    $elementAdmin->$setter($produit);
                }
            }

            // Gérer le téléchargement de la vidéo
            $videoFile = $form->get('videoFile')->getData();
            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    $videoFile->move(
                        $this->getParameter('videos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement
                }

                $elementAdmin->setVideoFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_element_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('element_admin/edit.html.twig', [
            'element_admin' => $elementAdmin,
            'form' => $form,
        ]);
    }
    // #[Route('/{id}', name: 'app_element_admin_delete', methods: ['POST'])]
    // public function delete(Request $request, ElementAdmin $elementAdmin, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$elementAdmin->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($elementAdmin);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_element_admin_index', [], Response::HTTP_SEE_OTHER);
    // }
}
