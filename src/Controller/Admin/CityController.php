<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/city')]
class CityController extends AbstractController
{
    #[Route('/', name: 'admin_city_index')]
    public function index(CityRepository $repo): Response
    {
        return $this->render('admin/city/index.html.twig', [
            'cities' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_city_new')]
    public function new(Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();

            return $this->redirectToRoute('admin_city_index');
        }

        return $this->render('admin/city/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_city_edit')]
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_city_index');
        }

        return $this->render('admin/city/edit.html.twig', [
            'form' => $form->createView(),
            'city' => $city,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_city_delete')]
    public function delete(City $city): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($city);
        $em->flush();

        return $this->redirectToRoute('admin_city_index');
    }
}
