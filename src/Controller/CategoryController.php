<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\CategoryType;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index")
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * @Route("/add", name="category_add")
     */
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
      $category = new Category();
      $form = $this->createForm(CategoryType::class, $category);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($category);
        $em->flush();
        return $this->redirectToRoute('category_index');
      }

      return $this->render('category/create.html.twig', [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/edit/{id}", name="category_edit", requirements={"id"="\d+"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
      $rep = $doctrine->getRepository(Category::class);
      $category = $rep->find($id);
      $form = $this->createForm(CategoryType::class, $category);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute('category_index');
      }

      return $this->render('category/create.html.twig', [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/delete/{id}", name="category_delete", requirements={"id"="\d+"})
     */
    public function delete(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
      $rep = $doctrine->getRepository(Category::class);
      $category = $rep->find($id);
      $em = $doctrine->getManager();
      $em->remove($category);
      $em->flush();

      return $this->redirectToRoute('category_index');
    }
}
