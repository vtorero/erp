import { SelectionModel } from '@angular/cdk/collections';
import { Component,  OnInit, ViewChild } from '@angular/core';
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
import { Venta } from 'app/modelos/venta';
import { VerCompraComponent } from '../ver-compra/ver-compra.component';
import { Compra } from '../../../modelos/compra';
import { ExportarComprasComponent } from '../../../dialog/exportar-compras/exportar-compras.component';
import { Global } from 'app/global';

function sendInvoice(data,url) {
  fetch(url, {
    method: 'post',
    headers: {
      'Content-Type': 'application/vnd.ms-excel'
    },
    body:data
  })
    .then(response => response.blob())
    .then(blob => {
      var link = document.createElement('a');
      link.href = window.URL.createObjectURL(blob);
      link.download = "compras.xls";
      link.click();
    });
}


@Component({
  selector: 'app-listado-compras',
  templateUrl: './listado-compras.component.html',
  styleUrls: ['./listado-compras.component.css']
})



export class ListadoComprasComponent implements OnInit {
  public selectedMoment = new Date();
  public selectedMoment2 = new Date();
  position = new FormControl('below');
  buscador:boolean=false;
  dataSource: any;
  selectedRowIndex:any;
  cancela: boolean = false;
  public id_estado:any=1;
  selection = new SelectionModel(false, []);
  displayedColumns = ['id','cliente','tipoDoc','nro_documento','serie_documento','fechaPago','nombre','valor_total','monto_pendiente','fecha','opciones'];
  dataEstados = [{ id: 1, value: 'Registrado' }, { id: 2, value: 'Anulado'}];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  constructor(public dialog: MatDialog,
    private _snackBar: MatSnackBar,
    private api: ApiService,
    public dialog2: MatDialog
      ) { }

  ngOnInit(): void {
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


consultar(){
  var fec1 = this.selectedMoment.toDateString().split(" ",4);
  var fec2 = this.selectedMoment2.toDateString().split(" ",4);
  let ini=fec1[1]+fec1[2]+fec1[3];
  let fin=fec2[1]+fec2[2]+fec2[3];

  this.api.consultaCompras(ini,fin,this.id_estado).subscribe(data=>{
    this.dataSource = new MatTableDataSource();
    this.dataSource.data = data;
    this.empTbSort.disableClear = true;
    this.dataSource.sort = this.empTbSort;
    this.dataSource.paginator = this.paginator;


  })

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
    this.api.getApi('compras').subscribe(x => {
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
    const dialog= this.dialog.open(VerCompraComponent, {
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

  openExportar(enterAnimationDuration: string, exitAnimationDuration: string){
    const dialogo2=this.dialog.open(ExportarComprasComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data: {
        clase:'Exportar'
      },
    });
    dialogo2.afterClosed().subscribe(ux => {
      console.log(ux);
      var fec1 = ux.fechainicio.toDateString().split(" ",4);
      var fec2 = ux.fechafin.toDateString().split(" ",4);
      let ini=fec1[1]+fec1[2]+fec1[3];
      let fin=fec2[1]+fec2[2]+fec2[3];
      console.log("inicio",ini);
      console.log("fin",fin);
     const datos ={
      fechaincio:ini,
      fechafin:fin
     }
     let json = JSON.stringify(datos);
      sendInvoice(json,Global.BASE_API_URL+'reportes.php/compras');

      //this.facturar(ux);
     });

  }


  openDelete(enterAnimationDuration: string, exitAnimationDuration: string){
  const dialogo2=this.dialog.open(AddClienteComponent, {
    width: 'auto',
    enterAnimationDuration,
    exitAnimationDuration,
    data: {
      clase:'DelCompra',
      compra:this.selectedRowIndex
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
eliminar(art:Compra) {
  console.log("art",art);
  if(art){
  this.api.delCompra(art).subscribe(
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
  const dialogo2 = this.dialog2.open(VerCompraComponent, {
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
