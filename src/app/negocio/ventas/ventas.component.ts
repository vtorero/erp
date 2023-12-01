import { Component,  OnInit, ViewChild } from '@angular/core';
import { ApiService } from 'app/api.service';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { Details } from '../../modelos/details';
import { MatDialog } from '@angular/material/dialog';
import { ModPrecioComponent } from '../../dialog/mod-precio/mod-precio.component';
import { ModCantidadComponent } from '../../dialog/mod-cantidad/mod-cantidad.component';
import { ModDescuentoComponent } from '../../dialog/mod-descuento/mod-descuento.component';
import { SelecTerminalComponent } from '../../dialog/selec-terminal/selec-terminal.component';
import { RegistroVentaComponent } from '../../dialog/registro-venta/registro-venta.component';
import { Router } from '@angular/router';






interface Elemento {
  id:number;
  nombre: string;
  almacen:number,
  cantidad: number;
  pendiente: number;
  precio:number;
  descuento:number,
  detalle:any
}


@Component({
  selector: 'app-ventas',
  templateUrl: './ventas.component.html',
  styleUrls: ['./ventas.component.css']
})



export class VentasComponent implements OnInit {
  selectedRowIndex:any;
  usuario:string;

  criterio:string='';
  categoria:string='';
  subcategoria:string='';
  familia:string='';

  sucursal:string='';
  sucursal_id:string='';
  dataCategoria:any;
  dataSubCategoria:any;
  dataFamilia:any;
  dataSource: any;
  dataTabla:any;
  totalMonto:number=0;
  totalDescuento:number=0;
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
    private router:Router


  ) { }

  ngOnInit(): void {
    if(this.api.getCurrentUser==false){
      this.router.navigate(['']);
      }

    let suc=  sessionStorage.getItem("sucursal_id");

    if(suc==null){
    this.openTerminal('20ms','20ms');
    }else{
      this.sucursal_id=suc;
        this.api.getApiTablaCriterio('sucursales',this.sucursal_id).subscribe(d => {
        this.sucursal=d[0]['nombre'];
        sessionStorage.setItem("sucursal_id",this.sucursal_id);
         });
    }

    this.usuario=localStorage.getItem("currentNombre");
    this.renderDataTable()
    this.dataTabla = this.api.getLinea();
    this.getCate();
  }


  public seleccionarCategoria(event) {
    const value = event.value;
    console.log(value);
    this.categoria=value;
    this.api.BuscarPorCategoria(value).subscribe(x => {
      this.dataSubCategoria=x;
      this.loading=false
    });
    this.api.BuscarPorFamilia(value,this.subcategoria,this.familia,'categoria').subscribe(x => {
      this.dataSource=x;
      this.loading=false
     });
 }

 public seleccionarSubcategoria(event) {
   const value = event.value;
   this.subcategoria=value;
   console.log("subcategoria",this.familia);
   console.log(value)
   this.api.BuscarPorSubcategoria(value).subscribe(x => {
   this.dataFamilia=x;
   this.loading=false
  });
  this.api.BuscarPorFamilia(this.categoria,value,this.familia,'subcategoria').subscribe(x => {
    this.dataSource=x;
    this.loading=false
   });
}

public seleccionarFamilia(event) {
  const value = event.value;
  console.log(value)
  this.api.BuscarPorFamilia(this.categoria,this.subcategoria,value,'familia').subscribe(x => {
  this.dataSource=x;
  this.loading=false
 });
}

 getCate(): void {
    this.api.getApi('categorias').subscribe(data => {
      if(data) {
        this.dataCategoria = data;
      }
    } );
  }

  getSubCategoria(): void {
    this.api.getApi('sub_categorias').subscribe(data => {
      if(data) {
        this.dataSubCategoria = data;
      }
    } );
  }

  getFamilia(): void {
    this.api.getApi('familia').subscribe(data => {
      if(data) {
        this.dataFamilia = data;
      }
    } );
  }

renderDataTable() {
  this.loading=true;
  this.selectedRowIndex=null
  this.api.getApi('articulos').subscribe(x => {
    this.loading=false;
   // this.dataSource = new MatTableDataSource();
     this.dataSource = x;
   //this.empTbSort.disableClear = true;
    //this.dataSource.sort = this.empTbSort;
    //this.dataSource.paginator = this.paginator;

    },
    error => {
      console.log('Error de conexion de datatable!' + error);
    });
}

applyFilter(filterValue: string) {
  this.criterio=filterValue
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
    array.push({ id:elemento.id,nombre:elemento.nombre,almacen:0,cantidad:cantidad,pendiente:0,precio:elemento.precio,descuento:desc,detalle:null});

  }
  this.sumarMonto(array)
    // Si el elemento no existe, agregarlo al array

  }

  sumarMonto(array: Details[]){
    this.totalMonto=0;

 array.forEach(item =>{this.totalMonto+=item.cantidad*item.precio-(item.descuento*item.cantidad)})
  }


enviarProducto(id:number,nombre:string,cantidad:number,precio:number){
 this.sumarCantidadSiExiste(this.dataRecibo, {id:id,nombre: nombre,almacen:0,precio:precio, cantidad:cantidad,pendiente:0,descuento:0,detalle:null},1,0);

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
        if(ux.descuento>0){
        //dato.precio = ux.precio-(ux.precio*(ux.descuento/100));
        dato.descuento=(dato.precio*(ux.descuento/100));

      }
      }
   });
   this.sumarMonto(this.dataRecibo)
   console.log("datarecibo",this.dataRecibo)
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
   console.log("cantidad",this.dataRecibo)
   this.sumarMonto(this.dataRecibo)
   });

}

openTerminal(enterAnimationDuration: string, exitAnimationDuration:string){

  const dialogo2=this.dialog.open(SelecTerminalComponent,{width: 'auto',enterAnimationDuration,exitAnimationDuration,disableClose: true , data: {},
  });
   dialogo2.afterClosed().subscribe(ux => {
     this.api.getApiTablaCriterio('sucursales',ux.id).subscribe(d => {
      this.sucursal=d[0]['nombre'];
      sessionStorage.setItem("sucursal_id",ux.id);
       });

   });
  }

  openRegistro(enterAnimationDuration: string, exitAnimationDuration:string){

    const dialogo2=this.dialog.open(RegistroVentaComponent,{width: '840px',enterAnimationDuration,exitAnimationDuration ,disableClose: true,
    data: {precio:this.totalMonto,
           detalle:this.dataRecibo
    },
    });
     dialogo2.afterClosed().subscribe(ux => {
      while(this.dataRecibo.length){
       this.dataRecibo.pop();
      }
      this.totalMonto=0;

     /*  this.api.getApiTablaCriterio('sucursales',ux.id).subscribe(d => {

        this.sucursal=d[0]['nombre'];
        localStorage.setItem("sucursal_id",ux.id);
         });*/

     });
    }


  borrarItem(id){
    console.log(id)
    this.dataRecibo.splice(id,1)
    this.sumarMonto(this.dataRecibo)

  }



}
