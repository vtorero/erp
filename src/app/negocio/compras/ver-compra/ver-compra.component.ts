import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { ApiService } from 'app/api.service';
import { Venta } from 'app/modelos/venta';
import { Details } from '../../../modelos/details';
import { EntregaFinalComponent } from '../../../dialog/entrega-final/entrega-final.component';
import { ModCantidadComponent } from 'app/dialog/mod-cantidad/mod-cantidad.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { DetaPagos } from '../../../modelos/detapagos';
import { PagoPendienteComponent } from 'app/dialog/pago-pendiente/pago-pendiente.component';
import { Compra } from '../../../modelos/compra';

@Component({
  selector: 'app-ver-compra',
  templateUrl: './ver-compra.component.html',
  styleUrls: ['./ver-compra.component.css']
})
export class VerCompraComponent implements OnInit {
  displayedColumns = ['id_producto', 'nombre', 'cantidad','pendiente','precio','subtotal'];
  displayedColumnsPago = ['id', 'tipoPago', 'monto','monto_pendiente','fecha_registro'];
  dataClientes:any;
  dataDetalle:any;
  dataProveedores:any;
  dataPagos:any;
  exampleArray:any;
  sucursales:any;
  constructor(
    public dialog: MatDialog,
    private api:ApiService,
    private _snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data:Compra,
    @Inject(MAT_DIALOG_DATA) public detalle:Details,
    @Inject(MAT_DIALOG_DATA) public detapago:DetaPagos,

  ) { }

  ngOnInit(): void {
    this.api.GetDetalleCompra(this.data.id).subscribe(x => {
      this.dataDetalle = new MatTableDataSource();
      this.exampleArray=x;
      this.dataDetalle=this.exampleArray
      this.data.detalleVenta=this.exampleArray;

      });

      this.api.GetDetallePagoCompra(this.data.id).subscribe(d => {
        this.dataPagos = new MatTableDataSource();
        this.exampleArray=d;
        this.dataPagos=this.exampleArray


        });

    this.getProveedor();
    this.cargaSucursales();

  }

  openMontoPendiente(enterAnimationDuration: string, exitAnimationDuration: string,id:number,id_venta:number,monto:number){
    let index=0;
    const dialogo2=this.dialog.open(PagoPendienteComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modPendiente',id:id,venta:id_venta,monto:monto},
    });
     dialogo2.afterClosed().subscribe(ux => {
      console.log("wsss",ux)
        this.api.actualizaMonto(id,id_venta,ux.cantidad,ux.monto).subscribe(
          data=>{
            this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            },
          erro=>{console.log(erro)}
            );

     });

  }


  openCantidad(enterAnimationDuration: string, exitAnimationDuration: string,id:number,id_producto:number,cantidad:number,nombre:string,id_venta:number){
    let index=0;
    const dialogo2=this.dialog.open(ModCantidadComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modCantidad',producto:id,cantidad:cantidad,nombre:nombre},
    });
     dialogo2.afterClosed().subscribe(ux => {
      this.dataDetalle.forEach(element => {
       if(element.id==id){
        this.dataDetalle[index].pendiente=ux.cantidad;
        this.api.actualizaPendientes(id_venta,id_producto,id,ux.cantidad).subscribe(
          data=>{
            this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            },
          erro=>{console.log(erro)}
            );


        }
       index++;
      });

      console.log("ux",id_venta)

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

  getProveedor(): void {
    this.api.getApi('proveedores').subscribe(data => {
      if(data) {
        this.dataProveedores = data;
      }
    } );
  }

}