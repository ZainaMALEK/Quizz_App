import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';

interface Category {
  name: string;
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



  constructor(private http: HttpClient){
    //this.url = 'https://yesno.wtf/api';
    this.url = 'http://127.0.0.1:8000/category/json'
    this.http.get(this.url)
      .subscribe(categories =>{
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
      console.log(questions);
    });
  }

  validQuestion(){
    this.questionIndex ++;
  }

  checkAnswer(question_id: number, answer_id: number){
    console.log();
  }
}
