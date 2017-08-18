import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-property-bindimng',
  template: `
          Property Binding
          <p>{{ name }}</p>
  `,
  styles: []
})
export class PropertyBindimngComponent  {
  @Input() name: string ;
}
