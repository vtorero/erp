import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-maintesoreria',
  templateUrl: './maintesoreria.component.html',
  styleUrls: ['./maintesoreria.component.css']
})
export class MaintesoreriaComponent implements OnInit {
  ventaTotal:any;
  montoPendiente:any;
  montoCompras:any;
  constructor() { }

  ngOnInit(): void {
  }

}
