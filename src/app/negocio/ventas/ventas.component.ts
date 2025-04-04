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
import { ModDespachoComponent } from '../../dialog/mod-despacho/mod-despacho.component';





interface Elemento {
  id:number;
  nombre: string;
  codigo:string,
  almacen:number,
  cantidad: number;
  despacho:number;
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

    let suc = sessionStorage.getItem("sucursal_id");

    if(suc==null){
    this.openTerminal('20ms','20ms');
    }else{
      this.sucursal_id=suc;
        this.api.getApiTablaCriterio('sucursales',this.sucursal_id).subscribe(d => {
        this.sucursal=d[0]['nombre'];
        sessionStorage.setItem("sucursal_id",this.sucursal_id);
         });
         if(localStorage.getItem('detalle')){
          this.dataRecibo=JSON.parse(localStorage.getItem('detalle'));
         }

         this.sumarMonto(this.dataRecibo);

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

onEnter(event: KeyboardEvent): void {
  const inputElement = event.target as HTMLInputElement;
   console.log('Enter presionado. Valor del input:', inputElement.value);
  setTimeout(() => {
    const producto = document.querySelector("body > app-root > app-admin-layout > div.wrapper > div.main-panel.ps > app-ventas > div > div > div > div:nth-child(1) > div.resultado > ul > li") as HTMLElement;

   if(producto!=undefined){
    producto.click();
    }else{
      alert("Código de Barras no existe");
    }
    inputElement.value='';
  }, 500); // 2000 milisegundos = 2 segundos



  // Puedes realizar otras acciones aquí
}

applyFilter(filterValue: string) {
  this.criterio=filterValue;
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
  //return array.some(item => item.id === elemento.id);
  return array && array.length > 0 ? array.some(item => item.id === elemento.id) : false;
}

sumarCantidadSiExiste(array: Details[], elemento: Elemento, cantidad: number,desc: number): void {
  if (this.existeElemento(array, elemento)) {
    // Si el elemento ya existe, sumarle la cantidad
    array.forEach(item => {
      if (item.id === elemento.id) {
        item.cantidad += cantidad;
        item.despacho+=cantidad;
      }

    });
  } else {
    if(array){
          array.push({id:elemento.id,nombre:elemento.nombre,codigo:elemento.codigo,almacen:0,cantidad:cantidad,despacho:cantidad,pendiente:0,precio:elemento.precio,descuento:desc,detalle:null});
        }
  }

  this.sumarMonto(array);
    // Si el elemento no existe, agregarlo al array

  }

  sumarMonto(array: Details[]){
    this.totalMonto=0;

 if(array){
    array.forEach(item =>{(this.totalMonto+=item.cantidad*item.precio-(item.descuento*item.cantidad)).toFixed(2)});
  }
}


enviarProducto(id:number,codigo:string,nombre:string,cantidad:number,precio:number){
  console.log(this.dataRecibo);

this.sumarCantidadSiExiste(this.dataRecibo, {id:id,nombre: nombre,codigo:codigo,almacen:0,precio:precio, cantidad:cantidad,despacho:cantidad,pendiente:0,descuento:0,detalle:null},1,0);
localStorage.setItem("detalle",JSON.stringify(this.dataRecibo))
 }

cancelar() {
  //this.dialog.closeAll();

}

proforma(){

  this.dataRecibo.forEach(item => {
    if (item.id) {
      //item.cantidad += cantidad;
      item.despacho=0;
    }
    });
    localStorage.setItem("detalle",JSON.stringify(this.dataRecibo));

  console.log(this.dataRecibo);

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
   localStorage.setItem("detalle",JSON.stringify(this.dataRecibo));
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
   localStorage.setItem("detalle",JSON.stringify(this.dataRecibo));
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
   localStorage.setItem("detalle",JSON.stringify(this.dataRecibo))
   this.sumarMonto(this.dataRecibo)
   });

}

openDespacho(enterAnimationDuration: string, exitAnimationDuration: string,id:number,cantidad:number,nombre:string){
  const dialogo2=this.dialog.open(ModDespachoComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
  data: {clase:'modCantidad',producto:id,cantidad:cantidad,nombre:nombre},
  });
   dialogo2.afterClosed().subscribe(ux => {
    this.dataRecibo.map(function(dato){
      if(dato.id == id){
        if(dato.cantidad<ux.despacho) {
          alert(`El despacho de: (${ux.despacho}) es mayor a la cantidad registrada: (${dato.cantidad})`);
          return;
        }else{
          dato.despacho = ux.despacho
          dato.pendiente=dato.cantidad-ux.despacho

        }

          //if(ux.despacho!=cantidad && ux.despacho<cantidad){

       /// console.log(dato)


      }
   });
   console.log("cantidad",this.dataRecibo)
   localStorage.setItem("detalle",JSON.stringify(this.dataRecibo));
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
  seleccionarProducto(event: KeyboardEvent) {
    event.preventDefault(); // Evita el comportamiento por defecto (por ejemplo, envío de formulario)
    console.log('Enter con producto');
  }

  openRegistro(enterAnimationDuration: string, exitAnimationDuration:string){

    const dialogo2=this.dialog.open(RegistroVentaComponent,{width:'900px',enterAnimationDuration,exitAnimationDuration ,disableClose: true,
    data: {precio:this.totalMonto,
           detalle:this.dataRecibo

    },
    });

     dialogo2.afterClosed().subscribe(ux => {
      //console.log(this.dataRecibo)
      this.totalMonto=0;
     })


    }


  borrarItem(id){
    this.dataRecibo.splice(id,1)
    this.sumarMonto(this.dataRecibo)
    localStorage.setItem("detalle",JSON.stringify(this.dataRecibo));

  }



}