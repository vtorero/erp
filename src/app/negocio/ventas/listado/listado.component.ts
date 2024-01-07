import { SelectionModel } from '@angular/cdk/collections';
import { Component, OnInit, ViewChild } from '@angular/core';
import { FormControl } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { ApiService } from 'app/api.service';
import { Usuario } from 'app/modelos/usuario';
import { Clientes } from 'app/modelos/clientes';
import { AddClienteComponent } from 'app/dialog/add-cliente/add-cliente.component';
import { VerVentaComponent } from '../ver-venta/ver-venta.component';
import { Venta } from 'app/modelos/venta';
import { Router } from '@angular/router';
declare function connetor_plugin(): void;



async function imprimirTicket(datos:any,form:any,cliente:string,direccion:string,telefono:string,pago:string,ticket:string,reciboneto:number,reciboigv:number,recibototal:number){
  const fecha = new Date();
  let imp = localStorage.getItem("impresora");
if(imp==""){
  console.log("impresora vacia")
  this.snackBar.open("Impresora no configurada", undefined, { duration: 8000, verticalPosition: 'bottom', panelClass: ['snackbar-error'] });
}

  let nombreImpresora = imp;
            let api_key = "123456"
            const conector = new connetor_plugin()
                        conector.textaling("center")
                       conector.img_url("https://lh-cjm.com/erp/assets/img/logo-erp-b-n.jpg");
                       conector.feed("1")
                       conector.fontsize("2")
                       conector.text("LAS HERMANITAS")
                        conector.fontsize("1")
                        conector.text("Ferretería y materiales de contruccion las")
                        conector.text("Hermanitas E.I.R.L.")
                        conector.text("lashermanitas_bertha@hotmail.com")
                        conector.text("LT. 9 MZ E COO. LA ESPERANZA - Santiago de Surco")
                        conector.text("Lima - Lima")
                        conector.text("RUC: 2053799520")
                        conector.feed("1")
                        conector.textaling("left")
                        conector.text("ADQUIRIENTE:")
                        conector.text(cliente)
                        if(direccion!=''){
                        conector.text("Direccion:"+direccion)
                        }
                        if(telefono!=''){
                          conector.text("Telefono:"+telefono)
                        }
                        conector.textaling("left")
                        conector.text("Fecha:"+fecha.toLocaleDateString() +" "+ fecha.getHours()+":"+fecha .getMinutes()+":"+fecha.getSeconds())
                        conector.text("Numero de ticket:"+ticket)
                        conector.feed("1")
                        conector.textaling("center")
                        conector.text("Descripcion      Cant.     Precio     Importe")
                        conector.text("===============================================")
                        for (let index = 0; index < datos.length; index++) {
                          const element = datos[index];
                          var precio =element.cantidad * element.precio -(element.descuento*element.cantidad)
                          var subtotal = element.cantidad * element.precio-(element.descuento*element.cantidad);
                          conector.textaling("left");
                          conector.text(index+1+") "+element.nombre);
                          conector.textaling("right");
                          conector.text("           "+element.cantidad+"        S/ "+(precio/element.cantidad).toFixed(2)+"        S/ "+subtotal.toFixed(2))
                          conector.textaling("center")
                          conector.text("---------------------------------------------")
                        }
                        conector.feed("1")
                        conector.textaling("center")
                        conector.text("==============================================")
                        conector.fontsize("1")
                        conector.textaling("right")
                        conector.text("Op. Gravadas: S/ "+reciboneto.toFixed(2))
                        if(form.value.igv>0){
                        conector.text("I.G.V: S/ "+reciboigv.toFixed(2))
                        }
                        conector.text("Total: S/ "+recibototal.toFixed(2))

                         conector.feed("1")
                        conector.textaling("center")
                        conector.text("**********************************")
                        conector.text("SON: "+pago+" SOLES")

                        const resp = await conector.imprimir(nombreImpresora, api_key);
                        if (resp === true) {

                             console.log("imprimio: "+resp)
                        } else {
                             console.log("Problema al imprimir: "+resp)

                        }

}


@Component({
  selector: 'app-listado',
  templateUrl: './listado.component.html',
  styleUrls: ['./listado.component.css']
})


export class ListadoComponent implements OnInit {
  position = new FormControl('below');
  buscador:boolean=false;
  dataSource: any;
  dataDetalle:any;
  clientetexto:string='Sin Cliente';
  telefonoCliente:string='';
direccioncliente:string='';
textoprecio:any;
numero_doc:string;
  selectedRowIndex:any;
  cancela: boolean = false;
  selection = new SelectionModel(false, []);
  displayedColumns = ['id','cliente','tipoDoc','fechaPago','nombre','valor_total','monto_pendiente','pendientes','observacion','opciones'];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  constructor(public dialog: MatDialog,
    private _snackBar: MatSnackBar,
    private api: ApiService,
    public dialog2: MatDialog,
    private router:Router
  ) { }

  ngOnInit(): void {
    if(this.api.getCurrentUser==false){
      this.router.navigate(['']);
      }
    this.renderDataTable();
  }

  applyFilter(filterValue: string) {
    filterValue = filterValue.trim();
    filterValue = filterValue.toLowerCase();
    this.dataSource.filter = filterValue;
}

openBusqueda(){
  if(this.buscador){
    this.buscador=false;
  }else{
    this.buscador=true;
  }
}

  selected(row) {
    this.selectedRowIndex=row;
    console.log('selectedRow',row)
  }

  editar(){
    console.log(this.selectedRowIndex);
  }

  renderDataTable() {
    this.selectedRowIndex=null
    this.api.getApi('ventas').subscribe(x => {
      this.dataSource = new MatTableDataSource();
      this.dataSource.data = x;
      this.empTbSort.disableClear = true;
      this.dataSource.sort = this.empTbSort;
      this.dataSource.paginator = this.paginator;
      },
      error => {
        console.log('Error de conexion de datatable!' + error);
      });
  }

  openDialogEdit(enterAnimationDuration: string, exitAnimationDuration: string): void {
    if(this.selectedRowIndex){
    const dialog= this.dialog.open(VerVentaComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data: this.selectedRowIndex
      ,
    });
    dialog.afterClosed().subscribe(ux => {
      if (ux!= undefined)
      this.update(ux)
     });

  }else{
    this._snackBar.open('Debe seleccionar un registro','OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
  }
  }

  openImprimir(enterAnimationDuration: string, exitAnimationDuration: string){
    const dialogo2=this.dialog.open(AddClienteComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data: {
        datos:this.selectedRowIndex,
        clase:'Imprimir',
        cliente:this.selectedRowIndex
      },
    });
    dialogo2.afterClosed().subscribe(ux => {
      this.api.GetDetalleVenta(ux.datos.id).subscribe(x => {
        this.dataDetalle = new MatTableDataSource();
          //this.exampleArray=x;
        this.dataDetalle=x
        //this.data.detalleVenta=this.exampleArray;
  console.log("data detalle",this.dataDetalle)
        });
        this.api.getApiTablaCriterio('clientes',ux.datos.cliente.id).subscribe(data => {
          if(data[0].nombre) {
      this.clientetexto=data[0].nombre;
      this.numero_doc=data[0].num_documento;
      this.direccioncliente=data[0].direccion;
      this.telefonoCliente=data[0].telefono;
          }
        }); 

        this.api.getNumeroALetras(ux.datos.precio).subscribe(letra => {
          console.log("letra",letra)
        this.textoprecio=letra;
        });

      console.log(ux);
   //   imprimirTicket(this.dataDetalle,ux,this.clientetexto,this.direccioncliente,this.telefonoCliente,this.textoprecio,"T00"+sessionStorage.getItem("sucursal_id")+"-"+data['numero'],this.reciboneto,this.reciboigv,this.recibototal)
     });

  }


  openFacturar(enterAnimationDuration: string, exitAnimationDuration: string){
    const dialogo2=this.dialog.open(AddClienteComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data: {
        datos:this.selectedRowIndex,
        clase:'Facturar',
        cliente:this.selectedRowIndex
      },
    });
    dialogo2.afterClosed().subscribe(ux => {
      console.log(ux);
      this.facturar(ux);
     });

  }

  openDelete(enterAnimationDuration: string, exitAnimationDuration: string){
  const dialogo2=this.dialog.open(AddClienteComponent, {
    width: 'auto',
    enterAnimationDuration,
    exitAnimationDuration,
    data: {
      clase:'DelProvedor',
      cliente:this.selectedRowIndex
    },
  });
  dialogo2.afterClosed().subscribe(ux => {
    console.log("delete");
    this.eliminar(ux);
   });

}


  openDialog(enterAnimationDuration: string, exitAnimationDuration: string): void {
    const dialogo1 =this.dialog.open(AddClienteComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data: {
        num_documento:'',
        telefono:'',
        clase:'Usuario',
        cliente:this.selectedRowIndex
      },
    });
    dialogo1.afterClosed().subscribe(us => {
      if (us!= undefined)
       this.agregar(us)
     });


  }

  update(art:Clientes) {
    if(art){
    this.api.EditarCliente(art).subscribe(
      data=>{
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
        this.renderDataTable();
        },
      erro=>{console.log(erro)}
        );

  }
}


  agregar(art:Clientes) {
    if(art){
    this.api.GuardarCliente(art).subscribe(
      data=>{
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
        },
      erro=>{console.log(erro)}
        );
      this.renderDataTable();
  }
}
facturar(art:Venta) {
  console.log("art",art);
  if(art){
  this.api.facturaVenta(art).subscribe(
    data=>{
      this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
      },
    erro=>{console.log(erro)}
      );
    this.renderDataTable();
}
}
eliminar(art:Clientes) {
  console.log("art",art);
  if(art){
  this.api.delCliente(art).subscribe(
    data=>{
      this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
      },
    erro=>{console.log(erro)}
      );
    this.renderDataTable();
}
}


abrirEditar(cod: Venta) {
  console.log("venta",cod)
  const dialogo2 = this.dialog2.open(VerVentaComponent, {
    data: cod,
    disableClose: false
  });
  dialogo2.afterClosed().subscribe(art => {
    if (art != undefined){
    //console.log("cargans",this.cargando);
     this.update(art);
    }
  });
}


  clickedRows = new Set<Usuario>();

}
