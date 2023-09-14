import { Component, Inject, NgModule, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';
import { FormArray, FormBuilder,  Validators, FormControl } from '@angular/forms';
import ConectorPluginV3 from 'app/services/ConectorImpresora';
import { MatSnackBar } from '@angular/material/snack-bar';
import { EntregaParcialComponent } from '../entrega-parcial/entrega-parcial.component';
import { Global } from 'app/global';




@Component({
  selector: 'app-registro-venta',
  templateUrl: './registro-venta.component.html',
  styleUrls: ['./registro-venta.component.css']
})





export class RegistroVentaComponent implements OnInit {
  public MyForm = this.fb.group({
    tipoDoc: ['', Validators.required],
    vendedor: ['', Validators.required],
    cliente: ['0', Validators.required],
    pagos: this.fb.array([this.fb.group({
      tipoPago: ['Efectivo', Validators.required],
      montoPago: [0, [Validators.required, Validators.min(0.1)]]

    })]),
    vuelto:['',Validators.required],
    igv:[''],
    comentario:[''],
    impresoras:[''],
    total:[0],
    usuario:[''],
    sucursal:[''],
    entrega: [''],
  });

dataClientes:any;
dataCajas:any;
montoVuelto:any=0;
montoRecibido:any=0;
impresoras:any;
montoTotal:any;
public id_documento:number=0



  constructor(public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details,
    private api:ApiService,
    private fb:FormBuilder,
    private _snackBar: MatSnackBar,

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

openEntrega(enterAnimationDuration: string, exitAnimationDuration: string){
  const dialogo2=this.dialog.open(EntregaParcialComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
  data: this.data,
  });
   dialogo2.afterClosed().subscribe(ux => {
    console.log("len",ux.pendiente);



  });
}


alerta(){
if(this.MyForm.value.entrega){
this.openEntrega('20ms','20ms')
}
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




async getData() {
  try {
    this.impresoras = await ConectorPluginV3.obtenerImpresoras();
    console.log(this.impresoras)
  } catch (error) {
    console.error(error);
  }
}



   onSubmit():void{
    if(this.MyForm.invalid){
      this.MyForm.markAllAsTouched();
      return;
    }
    const conector = new ConectorPluginV3;
    conector.Iniciar()
    conector.EstablecerAlineacion(ConectorPluginV3.ALINEACION_CENTRO);
    conector.EscribirTexto("esto es una prueba");
    conector.EscribirTexto("esto es una prueba");
    conector.EscribirTexto("esto es una prueba");
    conector.EscribirTexto("esto es una prueba");
    conector.EscribirTexto("esto es una prueba");
    const respuesta =   conector.imprimirEn(this.MyForm.get('impresoras').value);
    if(respuesta){
      console.log("imprimio correcto")
console.log(respuesta)
    }else{
      console.log("imprimio incorecto")
    }

    this.api.guardaVentas(this.MyForm.value,this.data.detalle).subscribe(
      data=>{
        console.log("form",this.MyForm.value)
        console.log("detallesss",this.data.detalle)
        this._snackBar.open(data['messaje'],"OK",{verticalPosition:'bottom'});
       this.MyForm.reset();
       this.cancelar()

        },
      error=>{console.log(error)
        this._snackBar.open("Ocurrio un Error","OK",{verticalPosition:'bottom'});
        this.cancelar()
      }
        );
      //this.renderDataTable();
  }


  ngOnInit(): void {
    this.getCliente();
    this.getData();
    this.getCajas();
  const total = this.MyForm.get('total') as FormControl;
  total.setValue(this.data.precio);
  const usuario = this.MyForm.get('usuario') as FormControl;
  usuario.setValue(localStorage.getItem("currentId"));
  const sucursal = this.MyForm.get('sucursal') as FormControl;
  sucursal.setValue(localStorage.getItem("sucursal_id"));
  }

  getCliente(): void {
    this.api.getApi('clientes').subscribe(data => {
      if(data) {
        this.dataClientes = data;
      }
    } );
  }

  getCajas(): void {
    const idUsuario = localStorage.getItem("currentId")
    this.api.getCajasUsuario(idUsuario).subscribe(data => {
      if(data) {
        this.dataCajas = data;
      }
    } );
  }


  cancelar() {
    this.dialog.closeAll();
    this.data.precio=0

  }

  cambiaTicket(value: any){
      const total2 = this.MyForm.get('total') as FormControl;
      const igv2 = this.MyForm.get('igv') as FormControl;
      if(value=='Factura'){
      this.data.detalle.map(function(dato){
        console.log(parseFloat(dato.precio) + (dato.precio * Global.BASE_IGV))
        total2.setValue(parseFloat(dato.precio) + (dato.precio * Global.BASE_IGV));
        igv2.setValue('18');
        });
      }
      if(value=='Ticket'){
        total2.setValue(this.data.precio);

      }
  }

  cambiaVuelto(precio:number){

  let efectivo:number=0;
  let depositos=0;
  let total:number=0;
    this.MyForm.value.pagos.forEach(element => {
  if(element.tipoPago=="Efectivo"){
      console.log(element.tipoPago)
      efectivo+=element.montoPago;
    }else{
    depositos+=element.montoPago;
    }
    });
 total=depositos+efectivo;
if(depositos>precio){
  const vuelto = this.MyForm.get('vuelto') as FormControl;
  this._snackBar.open("El monto recibido es mayor al precio total","Aceptar",{verticalPosition:'bottom'});
   vuelto.setValue('');
}
    if(efectivo<0){
      const vuelto = this.MyForm.get('vuelto') as FormControl;
      this._snackBar.open("El monto recibido no es suficiente","Aceptar",{verticalPosition:'bottom'});
      vuelto.setValue('');
  }else if(efectivo==precio){
  const vuelto = this.MyForm.get('vuelto') as FormControl;
    vuelto.setValue(efectivo-precio)

}else{
    const vuelto = this.MyForm.get('vuelto') as FormControl;
    const tipoDoc = this.MyForm.get('tipoDoc') as FormControl;
    if(tipoDoc.value=="Factura"){
      console.log(efectivo - (precio * Global.BASE_IGV))
      vuelto.setValue(efectivo-(precio + (precio * Global.BASE_IGV)));

    }else{
     vuelto.setValue(efectivo-precio)
    }
  }

  }
}
