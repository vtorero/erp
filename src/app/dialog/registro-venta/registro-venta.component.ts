import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';
import { FormArray, FormBuilder, FormGroup, Validators, FormControl } from '@angular/forms';

@Component({
  selector: 'app-registro-venta',
  templateUrl: './registro-venta.component.html',
  styleUrls: ['./registro-venta.component.css']
})


export class RegistroVentaComponent implements OnInit {
  public MyForm = this.fb.group({
    tipoDoc: ['', Validators.required],
    vendedor: ['', Validators.required],
    cliente: ['', Validators.required],
    pagos: this.fb.array([this.fb.group({
      tipoPago: ['Efectivo', Validators.required],
      montoPago: [0, [Validators.required, Validators.min(0.1)]],
    })])
  });

dataClientes:any;
montoVuelto:any=0;
montoRecibido:any=0;
public id_documento:number=0



  constructor(public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details,
    private api:ApiService,
    private fb:FormBuilder
    ) { }

    get Pagos() {
      return this.MyForm.get('pagos') as FormArray;
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


onAddToPago():void{
  const PagoGroup = this.fb.group({
		tipoPago: ['', Validators.required],
    montoPago: ['', [Validators.required, Validators.min(0.1)]],
	});
	this.Pagos.push(PagoGroup);
}

onDeletePago(index:number):void{
  this.Pagos.removeAt(index);
}


   onSubmit():void{
    if(this.MyForm.invalid){
      this.MyForm.markAllAsTouched();
      return;
    }
    console.log(this.MyForm.value);
   // this.MyForm.reset();
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
