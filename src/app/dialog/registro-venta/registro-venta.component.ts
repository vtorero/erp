import { Component, Inject, NgModule, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';
import { FormArray, FormBuilder,  Validators, FormControl } from '@angular/forms';
import ConectorPluginV3 from 'app/services/ConectorImpresora';
import { MatSnackBar } from '@angular/material/snack-bar';
import { EntregaParcialComponent } from '../entrega-parcial/entrega-parcial.component';
import { Global } from 'app/global';
import { DecimalPipe } from '@angular/common';
declare function connetor_plugin(): void;



function imprSelec(nombre) {
  var ficha = document.getElementById(nombre);
  var ventimp = window.open(' ','popimpr','left=200,top=200,width=800,height=620');
  ventimp.document.write( ficha.innerHTML );
  //ventimp.document.close();
  ventimp.print();
  ventimp.close();
}



async function imprimirTicket(datos:any,form:any,cliente:string,direccion:string,telefono:string,pago:string,ticket:string,reciboneto:number,reciboigv:number,recibototal:number){
  const fecha = new Date();
  let imp = localStorage.getItem("impresora");
if(imp==""){
  console.log("impresora vacia")
  this.snackBar.open("Impresora no configurada", undefined, { duration: 8000, verticalPosition: 'bottom', panelClass: ['snackbar-error'] });
}

  let nombreImpresora = imp;
            let api_key = "123456"
            const conector = new connetor_plugin()
                        conector.textaling("center")
                       conector.img_url("https://lh-cjm.com/erp/assets/img/logo-erp-b-n.jpg");
                       conector.feed("1")
                       conector.fontsize("2")
                       conector.text("LAS HERMANITAS")
                        conector.fontsize("1")
                        conector.text("Ferretería y materiales de contruccion las")
                        conector.text(localStorage.getItem("sucursal"))
                        conector.text("lashermanitas_bertha@hotmail.com")
                        conector.text(localStorage.getItem("direccion"))
                        conector.text("Telefonos: "+localStorage.getItem("telefono"))
                        conector.text("Lima - Lima")
                        conector.text("RUC: 2053799520")
                        conector.feed("1")
                        conector.textaling("left")
                        conector.text("ADQUIRIENTE:")
                        conector.text(cliente)
                        if(direccion!=''){
                        conector.text("Direccion:"+direccion)
                        }
                        if(telefono!=''){
                          conector.text("Telefono:"+telefono)
                        }
                        conector.textaling("left")
                        conector.text("Fecha:"+fecha.toLocaleDateString() +" "+ fecha.getHours()+":"+fecha .getMinutes()+":"+fecha.getSeconds())
                        conector.text("Numero de ticket:"+ticket)
                        conector.feed("1")
                        conector.textaling("center")
                        conector.text("Descripcion      Cant.     Precio     Importe")
                        conector.text("===============================================")
                        for (let index = 0; index < datos.length; index++) {
                          const element = datos[index];
                          var precio =element.cantidad * element.precio -(element.descuento*element.cantidad)
                          var subtotal = element.cantidad * element.precio-(element.descuento*element.cantidad);
                          conector.textaling("left");
                          conector.text(index+1+") "+element.nombre);
                          conector.textaling("right");
                          conector.text("           "+element.cantidad+"        S/ "+(precio/element.cantidad).toFixed(2)+"        S/ "+subtotal.toFixed(2))
                          conector.textaling("center")
                          conector.text("---------------------------------------------")
                        }
                        conector.feed("1")
                        conector.textaling("center")
                        conector.text("==============================================")
                        conector.fontsize("1")
                        conector.textaling("right")
                        conector.text("Op. Gravadas: S/ "+reciboneto.toFixed(2))
                        if(form.value.igv>0){
                        conector.text("I.G.V: S/ "+reciboigv.toFixed(2))
                        }
                        conector.text("Total: S/ "+recibototal.toFixed(2))

                         conector.feed("1")
                        conector.textaling("center")
                        conector.text("**********************************")
                        conector.text("SON: "+pago+" SOLES")

                        const resp = await conector.imprimir(nombreImpresora, api_key);
                        if (resp === true) {

                             console.log("imprimio: "+resp)
                        } else {
                             console.log("Problema al imprimir: "+resp)

                        }

}


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
      tipoPago: ['', Validators.required],
      cuentaPago:['',Validators.required],
      montoPago: [0, [Validators.required, Validators.min(0.0)]]

    })]),
    montopendiente: [0, Validators.required],
    vuelto:['',Validators.required],
    igv:[0],
    comentario:[''],
    impresoras:[''],
    impresora:[''],
    neto:[0],
    total:[0],
    usuario:[''],
    sucursal:[''],
    entrega: [0],
    imprimir:[1],
  });

dataClientes:any;
dataVendedores:any;
vuelto:string='';
dataCajas:any;
dataMedios:any;
montoVuelto:any=0;
montoRecibido:any=0;
impresoras:any;
montoTotal:any;
clientetexto:string='Sin Cliente';
telefonoCliente:string='';
direccioncliente:string='';
numero_doc:string;
textoprecio:any;
reciboneto:number=0;
reciboigv:number=0;
recibototal:number=0;
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

   // this.impresoras = await ConectorPluginV3.obtenerImpresoras();



    //console.log(this.impresoras)
  } catch (error) {
    console.error(error);
  }
}

imprimir() {


  imprSelec("recibo");

}



   onSubmit():void{
    if(this.MyForm.invalid){
      this.MyForm.markAllAsTouched();
      return;
    }

console.log(this.data.detalle)
console.log(this.MyForm)
const print = this.MyForm.get('imprimir') as FormControl;
let impresora = this.MyForm.get('impresoras') as FormControl;
    this.api.guardaVentas(this.MyForm.value,this.data.detalle).subscribe(
      data=>{
        console.log("form",this.MyForm.value)
        console.log("detallesss",this.data.detalle)

        this.reciboneto=this.MyForm.value.neto;
         this.reciboigv=this.MyForm.value.igv;
         this.recibototal=this.MyForm.value.total;
        if(print.value==true){
          console.log("impresoras",impresora.value);
        //this.imprimir();
        //enviarTicketera(impresora.value,this.data.detalle)
        imprimirTicket(this.data.detalle,this.MyForm,this.clientetexto,this.direccioncliente,this.telefonoCliente,this.textoprecio,"T00"+sessionStorage.getItem("sucursal_id")+"-"+data['numero'],this.reciboneto,this.reciboigv,this.recibototal)
        }


        this._snackBar.open(data['messaje'],"OK",{duration:5000,verticalPosition:'bottom'});
       this.MyForm.reset();
       this.cancelar()

        },
      error=>{console.log(error)
        this._snackBar.open("Ocurrio un Error","OK",{duration:10000,verticalPosition:'bottom'});
        this.cancelar()
      }
        );
      //this.renderDataTable();
  }


  ngOnInit(): void {
    this.getMedioPago();
    this.getCliente();
    this.getData();
    this.getCajas();
    this.getVendedor();
  const total = this.MyForm.get('total') as FormControl;
  total.setValue(this.data.precio);
  const usuario = this.MyForm.get('usuario') as FormControl;
  usuario.setValue(localStorage.getItem("currentId"));
  const sucursal = this.MyForm.get('sucursal') as FormControl;
  sucursal.setValue(sessionStorage.getItem("sucursal_id"));



  }


  getVendedor(): void {
    this.api.getApi('vendedores').subscribe(data => {
      if(data) {
        this.dataVendedores = data;
      }
    } );
  }

  getCliente(): void {
    this.api.getApi('clientes').subscribe(data => {
      if(data) {
        this.dataClientes = data;
      }
    } );
  }

  getMedioPago():void{
      this.api.getApiTabla('/tipoPago').subscribe(data => {
        if(data) {
this.dataMedios=data;
        }
      } );
    }

  getCajas(): void {
    this.api.getApiTabla("/cajas").subscribe(data => {
      if(data) {
        this.dataCajas = data;
      }
    } );
  }

seleccionarCliente(event){
  this.api.getApiTablaCriterio('clientes',event.value).subscribe(data => {
    if(data[0].nombre) {
this.clientetexto=data[0].nombre;
this.numero_doc=data[0].num_documento;
this.direccioncliente=data[0].direccion;
this.telefonoCliente=data[0].telefono;
    }
  });
}

  onKey(value) {
    this.selectSearch(value);
  }
  selectSearch(value: string) {
    this.api.apiBuscadorCliente(value).subscribe(data => {
      if (data) {
        this.dataClientes = data;
      }
    });
    }
  cancelar() {
    this.dialog.closeAll();
    this.data.precio=0

  }

  cambiaTicket(value: any){
      const total2 = this.MyForm.get('total') as FormControl;
      const igv2 = this.MyForm.get('igv') as FormControl;
      const neto2 = this.MyForm.get('neto') as FormControl;
      if(value=='Factura'){
        console.log("data",this.data.detalle)
        let neto=0;
        let total=0;
        let igv=0;
        for (let index = 0; index < this.data.detalle.length; index++) {

          neto= neto + (this.data.detalle[index].precio/Global.EXTRAE_IGV)
          total= total + (this.data.detalle[index].precio/Global.EXTRAE_IGV) + ((this.data.detalle[index].precio/Global.EXTRAE_IGV) * Global.BASE_IGV)
          igv = igv +(this.data.detalle[index].precio/Global.EXTRAE_IGV * Global.BASE_IGV)
        }
        neto2.setValue(neto.toFixed(2));
        total2.setValue(total.toFixed(2));
        igv2.setValue(igv.toFixed(2));

       // total2.setValue(parseFloat(total2.value) + (total2.value * Global.BASE_IGV))
        //igv2.setValue(total2.value * Global.BASE_IGV)
      }
      if(value=='Ticket' || value=='Boleta'){
        total2.setValue(this.data.precio.toFixed(2));
        neto2.setValue(this.data.precio.toFixed(2))

      }
     //
  }

  cambiaVuelto(precio:number){
var vuelto = this.MyForm.get('vuelto') as FormControl;
const tipoDoc1 = this.MyForm.get('tipoDoc') as FormControl;
let efectivo:number=0;
let depositos=0;
let total:number=0;
    this.MyForm.value.pagos.forEach(element => {
      console.log(element)
  if(element.tipoPago=="Efectivo" || element.tipoPago=='Yape' || element.tipoPago=='Factura'){
      efectivo+=element.montoPago;
    }else{
    depositos+=element.montoPago;
    console.log("deposito",depositos)
    }
    });
 total=depositos+efectivo;
 if(precio>total){
 this.vuelto='Pendiente';
 const mpendiente = this.MyForm.get('montopendiente') as FormControl;
  mpendiente.setValue((precio-efectivo).toFixed(2));
 }
 else{
  this.vuelto='Vuelto';
 }
 vuelto.setValue((total-precio).toFixed(2));
 this.api.getNumeroALetras(precio.toString()).subscribe(letra => {
    console.log("letra",letra)
  this.textoprecio=letra;
  });
/*
  if(tipoDoc1.value=="Factura"){
    const total3 = this.MyForm.get('total') as FormControl;
    const igv3 = this.MyForm.get('igv') as FormControl;
    const neto3 = this.MyForm.get('neto') as FormControl;
    console.log("facturaaa")
    console.log("efectivo",efectivo)
    console.log("precio",precio/Global.EXTRAE_IGV);
      let precio_p = precio/Global.EXTRAE_IGV
      console.log("precio_p",precio_p);
    console.log("igv",(precio_p * Global.BASE_IGV).toFixed(2))
    vuelto.setValue((efectivo-(precio_p + (precio_p * Global.BASE_IGV))).toFixed(2));
   let total=((precio_p + (precio_p * Global.BASE_IGV))).toFixed(2);
   total3.setValue(total);
   igv3.setValue((precio_p * Global.BASE_IGV).toFixed(2))
   neto3.setValue((precio/Global.EXTRAE_IGV).toFixed(2))
    this.api.getNumeroALetras(total.toString()).subscribe(letra => {
    this.textoprecio=letra;
    });
  }

*/
}

  }





