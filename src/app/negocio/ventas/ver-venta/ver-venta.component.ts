import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { ApiService } from 'app/api.service';
import { Venta } from 'app/modelos/venta';
import { AnyMxRecord } from 'dns';

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
    private api:ApiService,
    @Inject(MAT_DIALOG_DATA) public data:Venta,

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
