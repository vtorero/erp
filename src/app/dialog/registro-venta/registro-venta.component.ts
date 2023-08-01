import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';

@Component({
  selector: 'app-registro-venta',
  templateUrl: './registro-venta.component.html',
  styleUrls: ['./registro-venta.component.css']
})
export class RegistroVentaComponent implements OnInit {
dataClientes:any;
montoVuelto:any=0;
montoRecibido:any=0;
public id_documento:number=0
  constructor(public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details,
    private api:ApiService
    ) { }

  ngOnInit(): void {
    this.getCliente()
  }

  getCliente(): void {
    this.api.getApi('clientes').subscribe(data => {
      if(data) {
        this.dataClientes = data;
      }
    } );
  }

  cancelar() {

    this.dialog.closeAll();

  }

  cambiaVuelto(recibido:number,precio:number){
     this.montoVuelto=(recibido-precio);
  }
}
