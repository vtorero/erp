
import { SelectionModel } from '@angular/cdk/collections';
import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatSort } from '@angular/material/sort';
import { ApiService } from 'app/api.service';
import { Usuario } from 'app/modelos/usuario';
import { Productos } from '../../modelos/producto';
import { AddProductoComponent } from '../../dialog/add-producto/add-producto.component';
import { Kardex } from 'app/modelos/kardex';

@Component({
  selector: 'app-kardex',
  templateUrl: './kardex.component.html',
  styleUrls: ['./kardex.component.css']
})
export class KardexComponent implements OnInit {
  public selectedMoment = new Date();
public selectedMoment2 = new Date();
fec1= this.selectedMoment.toDateString().split(" ",4);
fec2 = this.selectedMoment2.toDateString().split(" ",4);
fecha1:string=this.fec1[2]+'-'+this.fec1[1]+'-'+this.fec1[3];
fecha2:string=this.fec2[2]+'-'+this.fec2[1]+'-'+this.fec2[3];
  buscador:boolean=false;
  dataSource: any;
  dataDetalle: any;
  dataProductos:any;
  dataSucursales:any;
  selectedRowIndex:any;
  cancela: boolean = false;
  prod:Productos;
  kard:Kardex;
  public id_producto:any;
  public id_sucursal:any;
  public tipo_movimiento:any;
  public id_compra:any;
  public id_venta:any;
  selection = new SelectionModel(false, []);
  displayedColumns = ['contador','fecha_registro','almacen','comentario','id_venta','id_compra','cantidad_ingreso','cantidad_salida','precio','promedio','p_total'];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();

  constructor(public dialog: MatDialog,
    private _snackBar: MatSnackBar,
    private api: ApiService,
  ) { }

  ngOnInit(): void {
    this.renderDataTable();
    this.verSucursales();
  }

  applyFilter(filterValue: string) {
    filterValue = filterValue.trim();
    filterValue = filterValue.toLowerCase();
    this.dataSource.filter = filterValue;
}

reset(){
  this.renderDataTable();
}

Busqueda(){
  var fec1 = this.selectedMoment.toDateString().split(" ",4);
  var fec2 = this.selectedMoment2.toDateString().split(" ",4);
  let ini=fec1[1]+fec1[2]+fec1[3];
  let fin=fec2[1]+fec2[2]+fec2[3];
  this.api.BuscarKardex(this.id_producto,this.id_sucursal,this.tipo_movimiento,ini,fin,this.id_compra,this.id_venta).subscribe(
    data=>{
      this.dataSource = data;
      },
    erro=>{console.log(erro)}
      );
   // this.renderDataTable();
}



openBusqueda(){
  if(this.buscador){
    this.buscador=false;
  }else{
    this.buscador=true;
  }
}
public seleccionarCategoria(event) {
  const value = event.value;
  console.log(value);

if(value==0){
  this.renderDataTable()
}else{
  this.api.getInventarios(value).subscribe(x => {
    this.dataSource.data = x;
    this.empTbSort.disableClear = true;
    this.dataSource.sort = this.empTbSort;
    this.dataSource.paginator = this.paginator;
    },
    error => {
      console.log('Error de conexion de datatable!' + error);
    });
}

}

seleccionarProducto(event){
  this.api.getApiTablaCriterio('productos',event.value).subscribe(data => {
    if(data[0].nombre) {

    }
  });
}
onKey(value) {
  this.selectSearch(value);
}
selectSearch(value: string) {
  this.api.apiBuscadorProducto(value).subscribe(data => {
    if (data) {
      this.dataProductos = data;
    }
  });
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
    this.api.getApi('movimientos').subscribe(x => {
      //this.dataSource = new MatTableDataSource();
      this.dataSource = x;
      console.log("dat",x);
      //this.empTbSort.disableClear = true;
      //this.dataSource.sort = this.empTbSort;
      //this.dataSource.paginator = this.paginator;
      },
      error => {
        console.log('Error de conexion de datatable!' + error);
      });
  }


  verMovimientos(id:number){
    this.api.GetDetalleMovimiento(id).subscribe(detalle => {
      this.dataDetalle=detalle
      });
  }

verSucursales():void{

  this.api.getApiTabla('/sucursales').subscribe(data => {
    if(data) {
      this.dataSucursales = data;
    }
  } );

}


  openDialogEdit(enterAnimationDuration: string, exitAnimationDuration: string): void {
    if(this.selectedRowIndex){
      this.api.getSelectApi('articulo/',this.selectedRowIndex.id).subscribe(x => {
        const dialog= this.dialog.open(AddProductoComponent, {
          width: '800px',
          enterAnimationDuration,
          exitAnimationDuration,
          data: x[0]
        });
        dialog.afterClosed().subscribe(ux => {
          if (ux!= undefined)
          this.update(ux)
      });
     });
  }else{
    this._snackBar.open('Debe seleccionar un registro','OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
  }
  }

  openDelete(enterAnimationDuration: string, exitAnimationDuration: string){
  const dialogo2=this.dialog.open(AddProductoComponent, {
    width: 'auto',
    enterAnimationDuration,
    exitAnimationDuration,
    data: {
      clase:'DelProducto',
      producto:this.selectedRowIndex
    },
  });
  dialogo2.afterClosed().subscribe(ux => {
    console.log("delete");
    this.eliminar(ux);
   });

}


  openDialog(enterAnimationDuration: string, exitAnimationDuration: string): void {

    const dialogo1 =this.dialog.open(AddProductoComponent, {
      width: 'auto',
      enterAnimationDuration,
      exitAnimationDuration,
      data:new Productos('','','','','','','',0,'',0,'','','','Nuevo','','')
    });
    dialogo1.afterClosed().subscribe(us => {
      if (us!= undefined)
       this.agregar(us)
     });
  }

  update(art:Productos) {
    if(art){
      console.log("art",art);
    this.api.EditarProducto(art).subscribe(
      data=>{
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
        this.renderDataTable();
        },
      erro=>{console.log(erro)}
        );

  }
}


  agregar(art:Productos) {
    if(art){
    this.api.GuardarProducto(art).subscribe(
      data=>{
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
        },
      erro=>{console.log(erro)}
        );
      this.renderDataTable();
  }
}

eliminar(art:Productos) {
  console.log("art",art);
  if(art){
  this.api.delProducto(art).subscribe(
    data=>{
      this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
      },
    erro=>{console.log(erro)}
      );
    this.renderDataTable();
}
}



  clickedRows = new Set<Usuario>();

}
