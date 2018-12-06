<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

      public function findByFilters($category, $difficulty)
    {
        $qb = $this->createQueryBuilder('q');
        if ($category) {
          $qb
            ->andWhere('q.category = :category')
            ->setParameter('category', $category);
        }
        if ($difficulty) {
          $qb
            ->andWhere('q.difficulty = :difficulty')
            ->setParameter('difficulty', $difficulty);
        }
        return $qb
          ->orderBy('q.id', 'DESC')
          ->getQuery()
          ->getResult()
        ;
    }


    public function findByFilterAssoc($category,$difficulty){

      $connection= $this->getEntityManager()->getConnection();

      $sql = 'SELECT answer.label AS answer_label, question.label AS question_label, answer.id AS answer_id, question_id
      FROM answer
      JOIN question
      ON question.id = answer.question_id
      WHERE question.id>0'
      ;

      $bindings = array();

      if($category){
        $sql .=' AND question.category_id = :category';
        $bindings[':category']= $category;
      }

      if($difficulty){
        $sql .=' AND question.difficulty = :difficulty';
        $bindings[':difficulty']= $difficulty;
      }


      $query = $connection->prepare($sql);
      $query->execute($bindings);
      $answers= $query->fetchAll();

      //réorganisation des données



      $questions = [];
      $key = 'question_id';

      foreach($answers as $answer)
      {
         $questions[$answer['question_id']]['question']=[
           'id'=>$answer['question_id'],
           'label'=>$answer['question_label']
         ];
         $questions[$answer['question_id']]['answers'][]=[
           'id'=>$answer['answer_id'],
           'label'=>$answer['answer_label']
         ];
       }

      //chaque libélé de question est devenu ene clé(indice) du tableau $questions, a cette clé est associé  un tableau de réponse
      return $questions;
    }
}
