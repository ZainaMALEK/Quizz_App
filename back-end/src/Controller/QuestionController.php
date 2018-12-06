<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\AnswerType;
use App\Form\QuestionType;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Answer;

class QuestionController extends AbstractController
{
  /**
   * @Route("/question", name="question")
   */
   public function index(Request $request)
   {
     // récupération paramètres URL
     $category = $request->query->get('category');
     $difficult = $request->query->get('difficulty');
     $questions = $this->getDoctrine()
       ->getRepository(Question::class)
       //->findAll()
       ->findByFilters($category, $difficult)
       ;
     // filtres de recherche
     $categories = $this->getDoctrine()
       ->getRepository(Category::class)
       ->findAll();
     $difficulty = array(
       'Facile' => 1,
       'Intermédiaire' => 2,
       'Difficile' => 3
     );
     return $this->render('question/index.html.twig', [
         'questions' => $questions,
         'categories' => $categories,
         'difficulty' => $difficulty
     ]);
   }

   /**
    * @Route("/question/json", name="question_json")
    */
    public function index_json(Request $request)
    {
      // récupération paramètres URL
      $category = $request->query->get('category');
      $difficult = $request->query->get('difficulty');
      $questions = $this->getDoctrine()
        ->getRepository(Question::class)

        ->findByFilterAssoc($category, $difficult)
        ;

      return new JsonResponse($questions);
    }

  /**
   * @Route("/question/detail/{id}", name="question_detail")
   */
  public function detail($id, Request $request)
  {


        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        $answer = new Answer();
        $answer->setQuestion($question);
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
          $answer = $form->getData();
          $em = $this->getDoctrine()->getManager();
          $em->persist($answer);
          $em->flush();
          return $this->redirectToRoute('question');
        }
        return $this->render('question/detail.html.twig', [
            'question'=>$question, 'form'=>$form->createView()
        ]);

  }

  /**
   * @Route("/question/dif/{difficulty}", name="question_difficulty")
   */
  public function difficulty($difficulty)
  {

    $questions = $this->getDoctrine()
      ->getRepository(Question::class)
      ->findQuestionsByDifficulty($difficulty);

      return $this->render('question/index.html.twig', [
          'questions' => $questions
      ]);
  }


    /**
     * @Route("/question/add", name="question_add")
     */
    public function add(Request $request)
    {

        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);


        $form->handleRequest($request);
          if ($form->isSubmitted())
          {
            $question = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();
          }
          return $this->render('question/add.html.twig', [
            'form' => $form->createView()
          ]);

    }


    /**
     * @Route("/question/edit/{id}", name="question_edit")
     */

      public function edit($id, Request $request)
        {
          $em = $this->getDoctrine()->getManager();
          // récupération des données
          $question = $em
            ->getRepository(Question::class)
            ->find($id);

          $form = $this->createForm(QuestionType::class, $question);

          $form->handleRequest($request);
          if ($form->isSubmitted()) {
            // modifie l'object category avec les données postées
            $question = $form->getData();
            $em->flush();
            return $this->redirectToRoute('question');
          }
          return $this->render('question/edit.html.twig', [
            'form' => $form->createView()
          ]);
        }

        /**
         * @Route("/question/delete{id}", name="question_delete")
         */
        public function delete($id)
        {
            $em = $this->getDoctrine()->getManager();
            $question = $this->getDoctrine()
              ->getRepository(Question::class)
              ->find($id);
            $em->remove($question);
            $em->flush();
            return $this->redirectToRoute('question');
        }


        /**
     * @Route(
     * "/question/client/check",
     * name="question_client_check",
     * methods={"POST"}
     * )
     */
    public function client_check(Request $request)
    {
      // accès au body de la requête Ajax en POST
      // grâce à la méthode getContent()
      $request_body = json_decode($request->getContent());
      $question_id = $request_body->question_id;
      $question_answers = $request_body->answers;
      // vérifier les réponses données par le client
      // en rapport avec la question identifiée
      $success = true;
      // tableau des bonnes réponses
      $answers = $this->getDoctrine()
        ->getRepository(Answer::class)
        ->findCorrectByQuestionId($question_id);
      // vérification des réponses client
      if (sizeof($question_answers) !== sizeof($answers)) {
        $success = false;
      } else {
        // tableaux de même longueur
        foreach($question_answers as $question_answer) {
          // à chaque itération, vérifier que l'id
          // de la réponse existe dans le tableau
          // des bonnes réponses
          if (!$this->exists($question_answer, $answers, 'id')) {
            $success = false;
          }
        } // fin foreach
      } // fin if
      return new JsonResponse(
        array(
          'success' => $success,
          'answers' => $answers)
        );
    }



}
