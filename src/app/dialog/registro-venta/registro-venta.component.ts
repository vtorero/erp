import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-registro-venta',
  templateUrl: './registro-venta.component.html',
  styleUrls: ['./registro-venta.component.css']
})


export class RegistroVentaComponent implements OnInit {

public MyForm:FormGroup = this.fb.group({
  name:['',[Validators.required,Validators.minLength(3)]],
  modoPago:this.fb.array([
    ['Yape',Validators.required],
    ['BCP',Validators.required],
  ])
})
dataClientes:any;
montoVuelto:any=0;
montoRecibido:any=0;
public id_documento:number=0
  constructor(public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details,
    private api:ApiService,
    private fb:FormBuilder
    ) { }

get formasPago(){

  return this.MyForm.get('modoPago') as FormArray;

}

isValidField(field:string):boolean | null{

  return this.MyForm.controls[field].errors
  && this.MyForm.controls[field].touched;
}

isValidFieldInArray(formArray:FormArray,index:number){
  return formArray.controls[index].errors
  && formArray.controls[index].touched;
}

  getFieldError(field:string):string | null{

  if(!this.MyForm.controls[field]) return null;
  const errors = this.MyForm.controls[field].errors || {};

  for(const key of Object.keys(errors)){
  switch(key){
    case 'required':
      return 'Este campo es requerido';

      case 'minlength':
      return   `Minimo  ${ errors['minlength'].requiredLength } caracteres`;

  }

  }
}


   onSubmit():void{
    if(this.MyForm.invalid){
      this.MyForm.markAllAsTouched();
      return;
    }
    console.log(this.MyForm.value);
    this.MyForm.reset();
   }


  ngOnInit(): void {
    this.getCliente()
  }

  getCliente(): void {
    this.api.getApi('clientes').subscribe(data => {
      if(data) {
        this.dataClientes = data;
      }
    } );
  }

  cancelar() {

    this.dialog.closeAll();

  }

  cambiaVuelto(recibido:number,precio:number){
     this.montoVuelto=(recibido-precio);
  }
}
