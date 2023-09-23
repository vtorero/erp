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
import { AgregarInventarioComponent } from '../../dialog/agregar-inventario/agregar-inventario.component';

@Component({
  selector: 'app-main-inventario',
  templateUrl: './main-inventario.component.html',
  styleUrls: ['./main-inventario.component.css']
})
export class MainInventarioComponent implements OnInit {

  buscador:boolean=false;
  dataSource: any;
  selectedRowIndex:any;
  cancela: boolean = false;
  prod:Productos;
  selection = new SelectionModel(false, []);
  displayedColumns = ['producto_id','nombre','cantidad','fecha_actualizacion'];
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

  selected(row) {
    this.selectedRowIndex=row;
    console.log('selectedRow',row)
  }

  editar(){
    console.log(this.selectedRowIndex);
  }

  renderDataTable() {
    this.selectedRowIndex=null
    this.api.getApi('/inventario').subscribe(x => {
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
        const dialog= this.dialog.open(AgregarInventarioComponent, {
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

    const dialogo1 =this.dialog.open(AgregarInventarioComponent, {
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
