<?php

namespace App\Controller\Admin;

use App\Entity\PoiCategory;
use App\Form\PoiCategoryType;
use App\Repository\PoiCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/poicategory')]
class PoiCategoryController extends AbstractController
{
    #[Route('/', name: 'admin_poicategory_index')]
    public function index(PoiCategoryRepository $repo): Response
    {
        return $this->render('admin/poicategory/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_poicategory_new')]
    public function new(Request $request): Response
    {
        $category = new PoiCategory();
        $form = $this->createForm(PoiCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_poicategory_index');
        }

        return $this->render('admin/poicategory/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_poicategory_edit')]
    public function edit(Request $request, PoiCategory $category): Response
    {
        $form = $this->createForm(PoiCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_poicategory_index');
        }

        return $this->render('admin/poicategory/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_poicategory_delete')]
    public function delete(PoiCategory $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('admin_poicategory_index');
    }
}
