import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';
import { DetaPagos } from '../../modelos/detapagos';

@Component({
  selector: 'app-pago-pendiente',
  templateUrl: './pago-pendiente.component.html',
  styleUrls: ['./pago-pendiente.component.css']
})


export class PagoPendienteComponent implements OnInit {
  dataMedios:any;
  dataCajas:any;

  constructor(
    public dialog: MatDialog,
    private api:ApiService,
    @Inject(MAT_DIALOG_DATA) public data: DetaPagos) {

     }

  ngOnInit(): void {
  this.getMedioPago()
  this.getCajas();
  }

  getMedioPago():void{
    this.api.getApiTabla('/tipoPago').subscribe(data => {
      if(data) {
this.dataMedios=data;
      }
    } );
  }

getCajas(): void {
  const idUsuario = localStorage.getItem("currentId")
  this.api.getCajasUsuario(idUsuario).subscribe(data => {
    if(data) {
      this.dataCajas = data;
    }
  } );
}

  cancelar() {

    this.dialog.closeAll();

  }
}
