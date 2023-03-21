import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Usuario } from '../../modelos/usuario';

export interface DialogData {
  animal: 'panda' | 'unicorn' | 'lion';
}
@Component({
  selector: 'app-open-dialog',
  templateUrl: './open-dialog.component.html',
  styleUrls: ['./open-dialog.component.css']
})
export class OpenDialogComponent implements OnInit {
  form:FormGroup;
  dataProducto:any;
seleccionados:string[]=[];
producto:any;
dataArray;
stock;
stockPeso;
dataExistencias:any;
dataUnidades = [{ id: 'NIU', tipo: 'Unidades' }, { id: 'KGM', tipo: 'Kilogramo' }];
constructor(private api:ApiService,  private fb:FormBuilder,
  @Inject(MAT_DIALOG_DATA) public data:Usuario
    ) {
      this.form=this.fb.group({
        nombre:['',Validators.required],
        correo:['',Validators.email],

        estado:['',Validators.required],
      });

    }
  getProductos(): void {
    this.api.getApi('productos').subscribe(data => {
      if(data) {
        this.dataProducto = data;
      }
    } );
  }
/*
  getProdExiste(id): void {
    this.api.getApi('inventarios/'+id).subscribe(data => {
      if(data) {
       this.dataExistencias=data;
      }
    });
  }
  onKey(value) {
  this.dataArray= [];
  this.selectSearch(value);
}
/*
selectSearch(value:string){
  this.api.getProductosSelect(value).subscribe(data => {
    if(data) {
      this.dataProducto = data;
    }
  } );

}

changemedida(ev,val){
if(ev.source){
  console.log("unidad",val);
}
}

  change(event)
  {
    console.log(event.source.value);
    this.data.mtoPrecioUnitario=event.source.value.precio;
    if(event.source.selected){
      this.seleccionados.push(event.source.value);
      }else{
          this.seleccionados.splice(event.source.index,1);
      }
      //console.log(event.source.value,event.source.selected);
      //console.log(this.seleccionados)
    }

    verificaCantidad(cantidad){
      this.stock=this.seleccionados;
      this.data.mtoValorUnitario=this.stock[0].precio;
      if(this.data.unidadmedida=="NIU"){
      if(Number(cantidad) > Number(this.stock[0].cantidad)){
        this.toastr.error("Inventario de " +this.stock[0].nombre+ " insuficiente");
         this.data.cantidad=null;
         cantidad;

    }
  }
    if(this.data.unidadmedida=="KGM"){
      if(Number(cantidad)> Number(this.stock[0].peso)){
        this.toastr.error("Inventario de " +this.stock[0].nombre+ " insuficiente");
         this.data.cantidad=null;
         cantidad;
      }
    }
    */



  ngOnInit() {
    //this.getProductos();
  }
}