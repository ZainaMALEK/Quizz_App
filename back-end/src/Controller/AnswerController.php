<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AnswerType;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Category;

class AnswerController extends AbstractController
{
  /**
   * @Route("/answer", name="answer")
   */
  public function index()
  {

      $answers = $this->getDoctrine()
                         ->getRepository(Answer::class)
                         ->findAll();

      return $this->render('answer/index.html.twig', [
          'answers' => $answers
      ]);
  }



  /**
   * @Route("/answer/{id}", name="answer_detail")
   */
  // public function detail($id)
  // {
  //
  //   $answer = $this->getDoctrine()
  //     ->getRepository(Answer::class)
  //     ->find($id);
  //
  //     return $this->render('answer/detail.html.twig', [
  //         'answer' => $answer
  //     ]);
  // }


    /**
     * @Route("/answer/add", name="answer_add")
     */
    public function add(Request $request)
    {

        $answer = new Answer();

        $form = $this->createForm(AnswerType::class, $answer);


        $form->handleRequest($request);
          if ($form->isSubmitted())
          {
            $answer = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();
          }
          return $this->render('answer/add.html.twig', [
            'form' => $form->createView()
          ]);

    }


    /**
     * @Route("/answer/edit/{id}", name="answer_edit")
     */

      public function edit($id, Request $request)
        {
          $em = $this->getDoctrine()->getManager();
          // récupération des données
          $answer = $em
            ->getRepository(Answer::class)
            ->find($id);

          $form = $this->createForm(AnswerType::class, $answer);

          $form->handleRequest($request);
          if ($form->isSubmitted()) {
            // modifie l'object category avec les données postées
            $answer = $form->getData();
            $em->flush();
            return $this->redirectToRoute('answer');
          }
          return $this->render('answer/edit.html.twig', [
            'form' => $form->createView()
          ]);
        }

        /**
         * @Route("/answer/delete{id}", name="answer_delete")
         */
        public function delete($id)
        {
            $em = $this->getDoctrine()->getManager();
            $answer = $this->getDoctrine()
              ->getRepository(Answer::class)
              ->find($id);
            $em->remove($answer);
            $em->flush();
            return $this->redirectToRoute('answer');
        }




}
