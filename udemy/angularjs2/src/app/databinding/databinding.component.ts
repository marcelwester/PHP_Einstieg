import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-databinding',
  templateUrl: './databinding.component.html',
  styles: [`
     .red-border {
       border: 1px solid red;
     }
  `]
  
})
export class DatabindingComponent  {
  aString = "Ich bin ein String";
  aNumber = 100;
  attachClass = false;
   
  constructor () {
    setTimeout (() => {
      this.aNumber += 200;
      this.attachClass = true;
    },3000);
  }

  onClick (event: Event) {
    console.log(event);
    alert('geklickt!');
  }
    
}
