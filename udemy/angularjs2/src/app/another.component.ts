import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-another',
  template: `
    <h1>Another Component</h1>
    <hr>
    <ng-content></ng-content>
  `,
  styleUrls: ['./another.component.css']
})
export class AnotherComponent  {

}
