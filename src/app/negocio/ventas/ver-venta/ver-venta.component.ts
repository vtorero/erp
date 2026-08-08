import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { ApiService } from 'app/api.service';
import { Details } from '../../../modelos/details';
import { EntregaFinalComponent } from '../../../dialog/entrega-final/entrega-final.component';
import { ModCantidadComponent } from 'app/dialog/mod-cantidad/mod-cantidad.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { DetaPagos } from '../../../modelos/detapagos';
import { PagoPendienteComponent } from 'app/dialog/pago-pendiente/pago-pendiente.component';
import { VentaVer } from 'app/modelos/ventaVer';
import { lastValueFrom } from 'rxjs';
import { BuscaProductoComponent } from 'app/dialog/busca-producto/busca-producto.component';
import { dataproc } from 'googleapis/build/src/apis/dataproc';



@Component({
  selector: 'app-ver-venta',
  templateUrl: './ver-venta.component.html',
  styleUrls: ['./ver-venta.component.css']
})
export class VerVentaComponent implements OnInit {
  displayedColumns = ['codigo', 'nombre', 'cantidad','pendiente','precio','subtotal','opciones'];
  displayedColumnsPago = ['id','caja','numero_operacion', 'monto','monto_pendiente','fecha_registro','opciones'];
  dataClientes:any;
  dataenvio:any;
  dataDetalle:any;
  dataVendedores:any;
  dataPagos:any;
  exampleArray:any;
  totalMonto:number=0;
  sucursales:any;
  cargando:boolean=true;
  esadmin:boolean=false;
  constructor(
    public dialog: MatDialog,
    private api:ApiService,
    private _snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data:VentaVer,
    @Inject(MAT_DIALOG_DATA) public detalle:Details,
    @Inject(MAT_DIALOG_DATA) public detapago:DetaPagos,

  ) { }

  async ngOnInit(): Promise<void> {
    this.getDetalle();
    this.getPagos();
      await this.getDataCliente(this.data.id_cliente);



    this.getCliente();
    this.cargaSucursales();
    this.getVendedor();
    if(localStorage.getItem("currentNombre")=="admin"){
      this.esadmin=true;
    }
  }

  getDetalle(){
    this.api.GetDetalleVenta(this.data.id).subscribe(x => {
      this.dataDetalle = new MatTableDataSource();
      this.exampleArray=x;
      this.dataDetalle=this.exampleArray
      this.data.detalleVenta=this.exampleArray;

      });
  }

  getPagos(){
    this.api.GetDetallePago(this.data.id).subscribe(d => {
      this.dataPagos = new MatTableDataSource();
      this.exampleArray=d;
      this.dataPagos=this.exampleArray


      });
  }

  async getDataCliente(id: any) {
    try {
      this.dataClientes = await lastValueFrom(this.api.getApiTablaCriterio('clientes', id));
    } catch (error) {
      console.error('Error al obtener datos del cliente:', error);
    }
    this.cargando=false;
  }

  /*getDataCliente(id:any) {
   return this.api.getApiTablaCriterio('clientes',id).subscribe(dat=>{
      this.dataClientes=dat;
    })
  }*/
save(d){
  console.log(d);
    this.api.observacion(d).subscribe(
      data=>{
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
           },
      erro=>{console.log(erro)}
    );

}

guardarCambios(d:any){
  this.dataenvio = {
    venta: d,
    pagos: this.dataPagos
  };

  this.api.actualizaVenta(this.dataenvio).subscribe(d=>{
  console.log(d);
  this._snackBar.open(d['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
  },
  error=>{
    console.log(error)
  });
  //console.log(this.dataenvio,d.id);
}



anularPago(pago: any) {
  if (!confirm('¿Desea eliminar este pago?')) {
    return;
  }
console.log(pago)
      this.dataPagos = this.dataPagos.filter(x => x.id != pago.id);
      this.api.EliminarPago(pago.id_venta,pago.id).subscribe(data=>{
        console.log(data);
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
        this.getPagos();
      },error=>{
        console.log(error)
      })
      // Si manejas estado de pago
      // Si deseas refrescar la tabla
      //this.dataPagos._updateChangeSubscription();

    }





anularProducto(id:any){

  if (!confirm('¿Desea eliminar este producto?')) {
    return;
  }
console.log(id)
//this.dataDetalle.splice(id,1)
this.dataDetalle = this.dataDetalle.filter(x => x.id != id);
this.data.detalleVenta=this.dataDetalle.filter(x => x.id != id);
this.sumarMonto(this.data.detalleVenta);
this.data.valor_total=this.totalMonto;
this.api.EliminarProducto(this.data.id,id).subscribe(data=>{
  console.log(data);
  this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
  this.getDetalle();
  this.getPagos();
},error=>{
  console.log(error)
})

}

sumarMonto(array: Details[]){
  this.totalMonto=0;

if(array){
  array.forEach(item =>{(this.totalMonto+=item.cantidad*item.precio-(item.descuento*item.cantidad)).toFixed(2)});
}
}

  getVendedor(): void {
    this.api.getApi('vendedores').subscribe(data => {
      if(data) {
        this.dataVendedores = data;
      }
    } );
  }

openProductos(enterAnimationDuration: string, exitAnimationDuration: string,id:number){
  const dialogo=this.dialog.open(BuscaProductoComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modPendiente',id:id},
    });
    dialogo.afterClosed().subscribe(ux => {

      const dataEnvio = {
        id_venta: this.data.id,
        prod:ux,
        sucursal:localStorage.getItem("id_suc")
      };
      console.log(dataEnvio)

      this.api.itemProducto(dataEnvio).subscribe(
        data=>{
             this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
              this.getDetalle();

      },
      err => {

        console.log(err);

        this._snackBar.open(
          err.error.messaje,
          'KO',
          {
            duration: 5000,
            horizontalPosition: 'center',
            verticalPosition: 'top'
          }
        );

      }

    );

});


}

  openMontoPendiente(enterAnimationDuration: string, exitAnimationDuration: string,id:number){
    let index=0;
    const dialogo2=this.dialog.open(PagoPendienteComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modPendiente',id:id},
    });
     dialogo2.afterClosed().subscribe(ux => {
           this.api.actualizaMonto(id,ux.numero,ux.cuentaPago,ux.monto_pendiente,ux.monto).subscribe(
          data=>{
               this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
                this.api.GetDetallePago(this.data.id).subscribe(d => {
                 this.dataPagos = new MatTableDataSource();
        this.exampleArray=d;
        this.dataPagos=this.exampleArray
            });
        },
        err => {

          console.log(err);

          this._snackBar.open(
            err.error.messaje,
            'Cerrar',
            {
              duration: 5000,
              horizontalPosition: 'center',
              verticalPosition: 'top'
            }
          );

        }

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
          if(ux.cantidad < this.dataDetalle[index].cantidad) {
           this.dataDetalle[index].pendiente=ux.cantidad;
           this.api.actualizaPendientesVenta(id_venta,id_producto,id,this.dataDetalle[index].pendiente,ux.cantidad).subscribe(
          data=>{
            this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            },
            erro=>{console.log(erro)}
            );
          }else{

          this._snackBar.open('Error en la cantidad ingresada debe ser menor a la cantidad de venta','OK',{duration:5000,horizontalPosition:'center',verticalPosition:'bottom'});
          }

        }
       index++;
      });

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

  cancelar() {
    this.dialog.closeAll();

  }

}
