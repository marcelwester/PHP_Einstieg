import { Component, OnInit,EventEmitter,Output } from '@angular/core';

@Component({
  selector: 'app-event-binding',
  template: `
    <div (click)="onClick()"></div>
  `,
  styles: [`
     div {
       width: 100px;
       height: 100px;
       background-color: gold;
     }
  `]
})
export class EventBindingComponent {
    @Output() clicked = new EventEmitter<string>();
    onClick() {
      this.clicked.emit('Neuer Text');
    }

}
