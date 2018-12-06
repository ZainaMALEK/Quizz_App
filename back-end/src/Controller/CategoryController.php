<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CategoryType;
use App\Entity\Category;



class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {

        $categories = $this->getDoctrine()
                           ->getRepository(Category::class)
                           ->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/add", name="category_add")
     */
    public function add(Request $request)
    {

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);


        $form->handleRequest($request);
          if ($form->isSubmitted())
          {
            $category = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
          }
          return $this->render('category/add.html.twig', [
            'form' => $form->createView()
          ]);

    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */

      public function edit($id, Request $request)
        {
          $em = $this->getDoctrine()->getManager();
          // récupération des données
          $category = $em
            ->getRepository(Category::class)
            ->find($id);

          $form = $this->createForm(CategoryType::class, $category);

          $form->handleRequest($request);
          if ($form->isSubmitted()) {
            // modifie l'object category avec les données postées
            $country = $form->getData();
            $em->flush();
            return $this->redirectToRoute('category');
          }
          return $this->render('category/edit.html.twig', [
            'form' => $form->createView()
          ]);
        }

        /**
         * @Route("/category/delete{id}", name="category_delete")
         */
        public function delete($id)
        {
            $em = $this->getDoctrine()->getManager();
            $category = $this->getDoctrine()
              ->getRepository(Category::class)
              ->find($id);
            $em->remove($category);
            $em->flush();
            return $this->redirectToRoute('category');
        }

        /**
         * @Route("/category/json", name="category_json")
         */
         public function index_json()
         {

            $categories= $this->getDoctrine()
                              ->getRepository(Category::class)
                              //->findAll();
                              ->findAllAssoc();
            return new Response (json_encode($categories));
         }



}
