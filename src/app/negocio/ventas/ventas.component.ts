import { Component,  OnInit, ViewChild } from '@angular/core';
import { ApiService } from 'app/api.service';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { Details } from '../../modelos/details';
import { AddProductoComponent } from 'app/dialog/add-producto/add-producto.component';
import { MatDialog } from '@angular/material/dialog';
import { ModPrecioComponent } from '../../dialog/mod-precio/mod-precio.component';
import { ModCantidadComponent } from '../../dialog/mod-cantidad/mod-cantidad.component';
import { ThisReceiver } from '@angular/compiler';
import { element } from 'protractor';
import { ModDescuentoComponent } from '../../dialog/mod-descuento/mod-descuento.component';


interface Elemento {
  id:number;
  nombre: string;
  cantidad: number;
  precio:number;
  descuento:number
}




@Component({
  selector: 'app-ventas',
  templateUrl: './ventas.component.html',
  styleUrls: ['./ventas.component.css']
})



export class VentasComponent implements OnInit {



  selectedRowIndex:any;
  dataSource: any;
  dataTabla:any;
  totalMonto:number=0;
  dataToDisplay = [];
  loading:boolean=false;
  dataRecibo:Details[] = []
  exampleArray: any[] = [];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  displayedColumns = ['id', 'nombre','precio'];

  constructor(
    public dialog: MatDialog,
    private api: ApiService,

  ) { }

  ngOnInit(): void {
    this.renderDataTable()
    this.dataTabla = this.api.getLinea();
  }



renderDataTable() {
  this.selectedRowIndex=null
  this.api.getApi('articulos').subscribe(x => {
    //this.dataSource = new MatTableDataSource();
     this.dataSource = x;
/*   this.empTbSort.disableClear = true;
    this.dataSource.sort = this.empTbSort;
    this.dataSource.paginator = this.paginator;
    */
    },
    error => {
      console.log('Error de conexion de datatable!' + error);
    });
}

applyFilter(filterValue: string) {
  console.log(filterValue)
  this.loading=true;
  filterValue = filterValue.trim();
  filterValue = filterValue.toLowerCase();
  if(filterValue!=''){
  this.api.BuscarProducto(filterValue).subscribe(x => {
    this.dataSource=x;
    this.loading=false

  }
  )
}else{
  this.renderDataTable()
  this.loading=false
}

}
existeElemento(array: Details[], elemento: Details): boolean {
  return array.some(item => item.id === elemento.id);
}

sumarCantidadSiExiste(array: Details[], elemento: Elemento, cantidad: number,desc: number): void {
  if (this.existeElemento(array, elemento)) {
    // Si el elemento ya existe, sumarle la cantidad
    array.forEach(item => {
      if (item.id === elemento.id) {
        item.cantidad += cantidad;
      }

    });
  } else {
    // Si el elemento no existe, agregarlo al array
    array.push({ id:elemento.id,nombre:elemento.nombre,cantidad:cantidad,precio:elemento.precio,descuento:desc});

  }
  this.sumarMonto(array)
    // Si el elemento no existe, agregarlo al array

  }

  sumarMonto(array: Details[]){
    this.totalMonto=0;
 array.forEach(item =>{this.totalMonto+=item.cantidad*item.precio})
  }

enviarProducto(id:number,nombre:string,cantidad:number,precio:number){
 this.sumarCantidadSiExiste(this.dataRecibo, {id:id,nombre: nombre,precio:precio, cantidad:cantidad,descuento:0},1,0);
 console.log("datarecob",this.dataRecibo)

}

cancelar() {
  this.dialog.closeAll();

}

openDescuento(enterAnimationDuration: string, exitAnimationDuration: string,id:number,precio:number,nombre:string){
  const dialogo2=this.dialog.open(ModDescuentoComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
  data: {clase:'modPrecio',producto:id,precio:precio,nombre:nombre},
  });
   dialogo2.afterClosed().subscribe(ux => {
    this.dataRecibo.map(function(dato){
      if(dato.id == id){
        if(ux.precio!=precio){
        dato.precio = ux.precio
      }
      }
   });
   this.sumarMonto(this.dataRecibo)
  });

}



openPrecio(enterAnimationDuration: string, exitAnimationDuration: string,id:number,precio:number,nombre:string){
  const dialogo2=this.dialog.open(ModPrecioComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
  data: {clase:'modPrecio',producto:id,precio:precio,nombre:nombre},
  });
   dialogo2.afterClosed().subscribe(ux => {
    this.dataRecibo.map(function(dato){
      if(dato.id == id){
        if(ux.precio!=precio){
        dato.precio = ux.precio
      }
      }
   });
   this.sumarMonto(this.dataRecibo)
  });

}

openCantidad(enterAnimationDuration: string, exitAnimationDuration: string,id:number,cantidad:number,nombre:string){
  const dialogo2=this.dialog.open(ModCantidadComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
  data: {clase:'modCantidad',producto:id,cantidad:cantidad,nombre:nombre},
  });
   dialogo2.afterClosed().subscribe(ux => {
    this.dataRecibo.map(function(dato){
      if(dato.id == id){
        if(ux.cantidad!=cantidad){
        dato.cantidad = ux.cantidad
      }
      }
   });
   this.sumarMonto(this.dataRecibo)
   });

}

}
