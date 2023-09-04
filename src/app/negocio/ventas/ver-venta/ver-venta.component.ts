import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Venta } from 'app/modelos/venta';

@Component({
  selector: 'app-ver-venta',
  templateUrl: './ver-venta.component.html',
  styleUrls: ['./ver-venta.component.css']
})
export class VerVentaComponent implements OnInit {
  dataClientes:any;

  constructor(
    private api:ApiService,
    @Inject(MAT_DIALOG_DATA) public data:Venta,

  ) { }

  ngOnInit(): void {
    this.getCliente();
  }



  getCliente(): void {
    this.api.getApi('clientes').subscribe(data => {
      if(data) {
        this.dataClientes = data;
      }
    } );
  }

}
