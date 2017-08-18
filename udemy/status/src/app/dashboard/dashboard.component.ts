import { Http,Response } from '@angular/http';
import { Component } from '@angular/core';


@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent  {
    bgcolor1 = 'white';
    bgcolor2 = 'white';
    bgcolor3 = 'white';
    bgcolor4 = 'white';   
    bgcolor5 = 'white';
    indx=0;
    //colors = ['red','blue','green','yellow','black','white'];


    constructor () {
       while (this.indx < 10) { 
         setTimeout(1000);
         this.bgcolor1='green';
         this.indx++;
       }
       
       this.bgcolor1='red';

       setTimeout (() => {
         this.bgcolor1='blue';
        },3000);
    }



 
}
