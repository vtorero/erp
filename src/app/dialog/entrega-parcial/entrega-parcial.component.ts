import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from '../../modelos/details';
import { ModPendienteComponent } from '../mod-pendiente/mod-pendiente.component';

@Component({
  selector: 'app-entrega-parcial',
  templateUrl: './entrega-parcial.component.html',
  styleUrls: ['./entrega-parcial.component.css']
})
export class EntregaParcialComponent implements OnInit {
  data:Details[] = []
  dataParcial:any
  constructor(
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public datos: Details,
  ) { }

  ngOnInit(): void {

  }

  openCantidad(enterAnimationDuration: string, exitAnimationDuration: string,id:number,pendiente:number,nombre:string){
    const dialogo2=this.dialog.open(ModPendienteComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modCantidad',producto:id,pendiente:pendiente,nombre:nombre},
    });
     dialogo2.afterClosed().subscribe(ux => {
      console.log("ux",ux);
      for (let index = 0; index < this.datos.detalle.length; index++) {
        if(this.datos.detalle[index].id==ux.producto){
        this.datos.detalle[index].pendiente=ux.pendiente;
      }
           }
            console.log("detalle",this.datos);


      });

    }
  }


