import { SelectionModel } from '@angular/cdk/collections';
import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { ApiService } from 'app/api.service';
import { Usuario } from 'app/modelos/usuario';
import { Productos } from '../../modelos/producto';
import { AddProductoComponent } from '../../dialog/add-producto/add-producto.component';
import { Global } from 'app/global';
import { OpenDialogComponent } from 'app/dialog/open-dialog/open-dialog.component';

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
      link.download = "productos.xls";
      link.click();
    });
}

@Component({
  selector: 'app-proveedores',
  templateUrl: './productos.component.html',
  styleUrls: ['./productos.component.css']
})

export class ProductosComponent implements OnInit {

  buscador:boolean=false;
  dataSource: any;
  selectedRowIndex:any;
  cancela: boolean = false;
  prod:Productos;
  selection = new SelectionModel(false, []);
  displayedColumns = ['id','codigo','codigobarras','nombre','categoria','subcategoria','familia','unidad','precio'];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  constructor(public dialog: MatDialog,
    private _snackBar: MatSnackBar,
    private api: ApiService,
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

enviaExcel(){

  sendInvoice({},Global.BASE_API_URL+'reportes.php/productos');
    }

  selected(row) {
    this.selectedRowIndex=row;
  }

  editar(){

  }

  renderDataTable() {
    this.selectedRowIndex=null
    this.api.getApi('articulos').subscribe(x => {
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
  const dialogo2=this.dialog.open(OpenDialogComponent, {
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
      //disableClose: true,
      enterAnimationDuration,
      exitAnimationDuration,
      data:new Productos('','','','','','','','',0,'',0,'','','','Nuevo','','')
    });
    dialogo1.afterClosed().subscribe(us => {
      if (us!= undefined)
       this.agregar(us);
      //this.renderDataTable();
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
    console.log("articulo",art.nombre);
    if(art.nombre!=""){
    this.api.GuardarProducto(art).subscribe(
      data=>{
        this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
        },
      erro=>{console.log(erro)

        this._snackBar.open('El producto debe tener una imagen de referencia','OK',{duration:5000,horizontalPosition:'right',verticalPosition:'top'});
      }

        );
     // this.renderDataTable();
  }
}

eliminar(art:Productos) {
  if(art){
  this.api.delProducto(art).subscribe(
    data=>{
      if(data['messaje']=='success'){
      this._snackBar.open(data['message'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
    } else{
      this._snackBar.open(data['message'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
    }
      },
    erro=>{console.log(erro)}
      );
    this.renderDataTable();
}
}



  clickedRows = new Set<Usuario>();

}
