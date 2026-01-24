<?php

namespace App\Controller\Admin;

use App\Entity\Poi;
use App\Form\PoiType;
use App\Repository\PoiRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/poi')]
class PoiController extends AbstractController
{
    private const ITEMS_PER_PAGE = 20; // Nombre de POI par page

    #[Route('/', name: 'admin_poi_index')]
    public function index(Request $request, PoiRepository $repo): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));

        $query = $repo->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE)
            ->getQuery();

        $paginator = new Paginator($query, true);
        $totalItems = count($paginator);
        $totalPages = (int) ceil($totalItems / self::ITEMS_PER_PAGE);

        return $this->render('admin/poi/index.html.twig', [
            'pois' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'admin_poi_new')]
    public function new(Request $request): Response
    {
        $poi = new Poi();
        $form = $this->createForm(PoiType::class, $poi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($poi);
            $em->flush();

            return $this->redirectToRoute('admin_poi_index');
        }

        return $this->render('admin/poi/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_poi_edit')]
    public function edit(Request $request, Poi $poi): Response
    {
        $form = $this->createForm(PoiType::class, $poi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_poi_index');
        }

        return $this->render('admin/poi/edit.html.twig', [
            'form' => $form->createView(),
            'poi' => $poi,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_poi_delete')]
    public function delete(Poi $poi): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($poi);
        $em->flush();

        return $this->redirectToRoute('admin_poi_index');
    }
}
