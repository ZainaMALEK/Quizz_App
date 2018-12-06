import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';

interface Category {
  name: string;
}

interface ResponseToClientChoice {
  success: boolean;
  answers: any[];
}
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Quizz App';
  answer = null;
  imgUrl = null;
  categories: any;
  category:number=0;
  difficulty:number =0;
  questions:any[] = null;
  url='';
  isQuizzRunning: boolean = false;
  questionIndex=0;

  clientChoice = {
    question_id: null,
    answers: []
  };

  isClientChoiceSent: boolean = false;
  isServerResponseReceived: boolean = false;
  feedback: string = '';
  score: number = 0;
  feedbackColor: string = '#000';
  noResult: boolean = false;
  goodAnswers = null;


  //au chargement de la page, on récuperes les catégories en JSON
  constructor(private http: HttpClient){
    //this.url = 'https://yesno.wtf/api';
    this.url = 'http://127.0.0.1:8000/category/json'
    this.http.get(this.url)
      .subscribe(categories =>{//la méthode subscribe decode le json
        this.categories = categories;
      });
  }

  runQuizz(){
    //Charger une collection de questions/réponses
    //On doit interroger une route par requete ajax fournissant les données( une route:endpoint)

    let url='http://127.0.0.1:8000/question/json';
    url+=`?category=${this.category}&difficulty=${this.difficulty}`;
    //console.log(url)
    this.http.get(url)
    .subscribe(res=>{

      let questions=[];

      //itération sur les clés de l'objet
      for(let k in  res){

        let question={
          'id': k,
          'label':res[k].question.label,
          'answers':res[k].answers
        };
        questions.push(question);
      }
      this.questions=questions;
      this.isQuizzRunning = true;

      //this.clientChoice.question_id = this.questions[this.questionIndex].id;
    });
  }



  checkAnswer(answer_id: number) {
    let index = this.clientChoice.answers.indexOf(answer_id);
    if (index === -1) {
      // answer_id non trouvé clientChoice.answers
      // => on l'ajoute
      this.clientChoice.answers.push(answer_id);
    } else {
      this.clientChoice.answers.splice(index, 1);
    }
    console.log(this.clientChoice.answers);
  }


  validQuestion() {
    this.isClientChoiceSent = true;
    // requête au serveur pour vérification du choix client
    let url = 'http://localhost:8000/question/client/check';
  

    this.http.post(url, this.clientChoice)
      .subscribe((res: ResponseToClientChoice) => {
        console.log(res);
        if (res.success) {
          // le client a fourni la/les bonne(s) réponse(s)
          this.feedback = 'Bien joué !';
          this.feedbackColor = '#6ee244';
          this.score++;
        } else {
          // le client a échoué
          this.feedback = 'Raté !';
          this.feedbackColor = '#bc0b0b';
        }
        this.goodAnswers = res.answers;
        this.isServerResponseReceived = true;
      })
  }



}
