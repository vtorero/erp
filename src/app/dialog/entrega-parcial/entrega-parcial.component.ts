import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from '../../modelos/details';
import { ModCantidadComponent } from '../mod-cantidad/mod-cantidad.component';
import { CajasComponent } from '../../seguridad/cajas/cajas.component';

@Component({
  selector: 'app-entrega-parcial',
  templateUrl: './entrega-parcial.component.html',
  styleUrls: ['./entrega-parcial.component.css']
})
export class EntregaParcialComponent implements OnInit {
  data:Details[] = []
  constructor(
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public datos: Details,
  ) { }

  ngOnInit(): void {
  }

  openCantidad(enterAnimationDuration: string, exitAnimationDuration: string,id:number,cantidad:number,nombre:string){
    const dialogo2=this.dialog.open(ModCantidadComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modCantidad',producto:id,cantidad:cantidad,nombre:nombre},
    });
     dialogo2.afterClosed().subscribe(ux => {
          //if(this.datos.cantidad!=cantidad){
            this.datos.detalle.map(function(dato){
              console.log("dato",dato)
              if(dato.id == id){

                dato.cantidad = ux.cantidad;


              }
              });
      //     }

        });
      }
    }


