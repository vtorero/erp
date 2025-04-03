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
declare function NumerosALetras(numero:number): any;

async function imprimirTicket(datos:any,form:any,cliente:string,direccion:string,telefono:string,pago:string,ticket:string,reciboneto:number,reciboigv:number,recibototal:number){

  let fecha = new Date(datos[0].fecha_registro);
//  console.log(fecha.toLocaleDateString() +" "+ fecha.getHours()+":"+fecha .getMinutes()+":"+fecha.getSeconds())
  

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
                       conector.text(localStorage.getItem("sucursal"))
                       conector.fontsize("1")
                       conector.text("Ferreteria y materiales de contruccion")
                       if(localStorage.getItem("email")!='-'){
                       conector.text("Email: "+localStorage.getItem("email"))
                       }
                        conector.text(localStorage.getItem("direccion"))
                        conector.text("Telefonos: "+localStorage.getItem("telefono"))
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
                        conector.text("Op. Gravadas: S/ "+form.datos.valor_neto)
                        if(form.datos.igv>0){
                        conector.text("I.G.V: S/ "+form.datos.igv)
                        }
                        conector.text("Total: S/ "+form.datos.valor_total)

                         conector.feed("1")
                        conector.textaling("center")
                        conector.text("**********************************")
                        conector.text("SON:"+pago)
                        try {
                          const resp = await conector.imprimir(nombreImpresora, api_key);
                          if (resp === true) {

                               console.log("imprimio: "+resp)
                          } else {
                               console.log("Problema al imprimir: "+resp)

                          }

                        } catch (error) {
                          console.log("Problema al imprimir: "+error);

                        }

                     }


@Component({
  selector: 'app-listado',
  templateUrl: './listado.component.html',
  styleUrls: ['./listado.component.css']
})


export class ListadoComponent implements OnInit {
  public selectedMoment = new Date();
  public selectedMoment2 = new Date();
  fec1= this.selectedMoment.toDateString().split(" ",4);
  fec2 = this.selectedMoment2.toDateString().split(" ",4);
  position = new FormControl('below');
  buscador:boolean=false;
  dataSource: any;
  dataCliente:any;
  dataDetalle:any;
  clientetexto:string='Sin Cliente';
  telefonoCliente:string='';
  direccioncliente:string='';
  currentname:string='';
  textoprecio:any;
  numero_doc:string;
  reciboneto:number=0;
  reciboigv:number=0;
  recibototal:number=0;
  selectedRowIndex:any;
  cancela: boolean = false;
  selection = new SelectionModel(false, []);
  displayedColumns = ['id','id_cliente','cliente','tipoDoc','fechaPago','nombre','valor_total','monto_pendiente','pendientes','estado','observacion','opciones'];
  dataEstados = [{ id: 1, value: 'Registrado' }, { id: 2, value: 'Anulado'}];
  public id_estado:any=1;
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
    this.currentname=localStorage.getItem("currentNombre");
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
      console.log("VEtas",x)
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

  consultar(){
    var fec1 = this.selectedMoment.toDateString().split(" ",4);
    var fec2 = this.selectedMoment2.toDateString().split(" ",4);
    let ini=fec1[1]+fec1[2]+fec1[3];
    let fin=fec2[1]+fec2[2]+fec2[3];
    console.log("inicio",ini);
    console.log("fin",fin);
    console.log("estado",this.id_estado)
    this.api.consultaVentas(ini,fin,this.id_estado).subscribe(data=>{
      this.dataSource = new MatTableDataSource();
      this.dataSource.data = data;
      this.empTbSort.disableClear = true;
      this.dataSource.sort = this.empTbSort;
      this.dataSource.paginator = this.paginator;


    })

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
      /*this.api.getNumeroALetras(ux.datos.valor_total).subscribe(letra => {
      this.textoprecio=letra;

      });*/
     // this.textoprecio=NumerosALetras(10);
     //this.textoprecio= ux.datos.valor_total.toString();
     this.textoprecio = NumerosALetras(ux.datos.valor_total);
     this.api.getApiTablaCriterio('clientes',ux.cliente.id_cliente).subscribe(data => {
     if(data[0].nombre) {
        this.clientetexto=data[0].nombre;
        this.numero_doc=data[0].num_documento;
        this.direccioncliente=data[0].direccion;
        this.telefonoCliente=data[0].telefono;
        }
        });
      this.api.GetDetalleVenta(ux.datos.id).subscribe(x => {
        console.log("ccc",x);
    imprimirTicket(x,ux,this.clientetexto,this.direccioncliente,this.telefonoCliente,this.textoprecio,"T00"+localStorage.getItem("id_suc")+"-"+ux.datos.id,this.reciboneto,this.reciboigv,this.recibototal)
        });
     });

  }

  wait(ms){
    var start = new Date().getTime();
    var end = start;
    while(end < start + ms) {
      end = new Date().getTime();
   }
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

  openAnular(enterAnimationDuration: string, exitAnimationDuration: string){
    const dialogo2=this.dialog.open(AddClienteComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data: {
        datos:this.selectedRowIndex,
        clase:'Anular',
        cliente:this.selectedRowIndex
      },
    });
    dialogo2.afterClosed().subscribe(ux => {
      console.log("anular",ux);
      this.anular(ux);
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

anular(art:Venta) {
  console.log("art",art);
  if(art){
  this.api.anularVenta(art).subscribe(
    data=>{
      this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
      },
    erro=>{console.log(erro)}
      );
    this.renderDataTable();
}
}


facturar(art:Venta) {
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
    this.renderDataTable();
     this.update(art);
    }
  });
}


  clickedRows = new Set<Usuario>();

}
