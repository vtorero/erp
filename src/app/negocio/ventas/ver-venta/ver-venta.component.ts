import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { ApiService } from 'app/api.service';
import { EntregaParcialComponent } from 'app/dialog/entrega-parcial/entrega-parcial.component';
import { Venta } from 'app/modelos/venta';
import { AnyMxRecord } from 'dns';
import { Details } from '../../../modelos/details';
import { EntregaFinalComponent } from '../../../dialog/entrega-final/entrega-final.component';
import { ModCantidadComponent } from 'app/dialog/mod-cantidad/mod-cantidad.component';

@Component({
  selector: 'app-ver-venta',
  templateUrl: './ver-venta.component.html',
  styleUrls: ['./ver-venta.component.css']
})
export class VerVentaComponent implements OnInit {
  displayedColumns = ['id_producto', 'nombre', 'cantidad','pendiente','precio','subtotal','borrar'];
  dataClientes:any;
  dataDetalle:any;
  exampleArray:any;
  sucursales:any;
  constructor(
    public dialog: MatDialog,
    private api:ApiService,
    @Inject(MAT_DIALOG_DATA) public data:Venta,
    @Inject(MAT_DIALOG_DATA) public detalle:Details,

  ) { }

  ngOnInit(): void {
    this.api.GetDetalleVenta(this.data.id).subscribe(x => {
      this.dataDetalle = new MatTableDataSource();
      this.exampleArray=x;
      this.dataDetalle=this.exampleArray
      this.data.detalleVenta=this.exampleArray;
      console.log("datadetalle",this.dataDetalle)
      });

    this.getCliente();
    this.cargaSucursales();
  }

  openCantidad(enterAnimationDuration: string, exitAnimationDuration: string,id:number,cantidad:number,nombre:string){
    let index=0;
    const dialogo2=this.dialog.open(ModCantidadComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modCantidad',producto:id,cantidad:cantidad,nombre:nombre},
    });
     dialogo2.afterClosed().subscribe(ux => {
      
      this.dataDetalle.forEach(element => {
       if(element.id==id){
        this.dataDetalle[index].pendiente=ux.cantidad;
       }
       index++;
      });

      console.log("ux",ux)
   
     });
  
  }

  openEntrega(enterAnimationDuration: string, exitAnimationDuration: string){
    console.log(this.dataDetalle)
    const dialogo2=this.dialog.open(EntregaFinalComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: this.dataDetalle,
    });
     dialogo2.afterClosed().subscribe(ux => {
      console.log("len",ux);

    });
  }



  cargaSucursales() {
    this.api.getApiTabla('/sucursales').subscribe(x => {
      this.sucursales = x;
      },
      error => {
        console.log('Error de conexion de datatable!' + error);
      });
  }

  getCliente(): void {
    this.api.getApi('clientes').subscribe(data => {
      if(data) {
        this.dataClientes = data;
      }
    } );
  }

}
