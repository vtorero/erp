import { Component, Inject, NgModule, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';
import { FormArray, FormBuilder,  Validators, FormControl } from '@angular/forms';
import ConectorPluginV3 from 'app/services/ConectorImpresora';
import { MatSnackBar } from '@angular/material/snack-bar';
import { EntregaParcialComponent } from '../entrega-parcial/entrega-parcial.component';
import { Global } from 'app/global';
declare function connetor_plugin(): void;



function imprSelec(nombre) {
  var ficha = document.getElementById(nombre);
  var ventimp = window.open(' ','popimpr','left=200,top=200,width=800,height=620');
  ventimp.document.write( ficha.innerHTML );
  //ventimp.document.close();
  ventimp.print();
  ventimp.close();
}



async function imprimirTicket(datos,form,cliente,direccion,pago){
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
                       conector.img_url("https://aprendeadistancia.online/erp/assets/img/logo-erp-bn.jpg");
                       conector.fontsize("2")
                       conector.text("LAS HERMANITAS")
                        conector.fontsize("1")
                        conector.text("Ferretería y materiales de contruccion las")
                        conector.text("Hermanitas E.I.R.L.")
                        conector.text("lashermanitas_bertha@hotmail.com")
                        conector.text("LT. 9 MZ E COO. LA ESPERANZA - Santiago de Surco")
                        conector.text("Lima - Lima")
                        conector.text("RUC: 2053799520")
                        conector.feed("1")
                        conector.textaling("left")
                        conector.text("ADQUIRIENTE")
                        conector.text(cliente)
                        conector.text(direccion)
                        conector.textaling("left")
                        conector.text("Fecha:"+fecha.toLocaleDateString())
                        conector.text("Descripcion        Cant.     Precio      Importe")
                        conector.text("-------------------------------------------")
                        for (let index = 0; index < datos.length; index++) {
                          const element = datos[index];
                          let precio =element.cantidad * element.precio -(element.descuento*element.cantidad)
                          conector.text(element.nombre)
                          conector.text("       "+element.cantidad+"          "+element.cantidad+"             S/ "+precio)
                        }
                        conector.feed("1")
                        conector.text("-------------------------------------------")
                        conector.fontsize("1")
                        conector.textaling("right")
                        conector.text("Op. Gravadas: "+form.value.neto)
                        conector.text("i.G.V: "+form.value.igv)
                        conector.text("Total: "+form.value.total)
                        //conector.qr("https://abrazasoft.com")
                        conector.feed("1")
                        conector.textaling("center")
                        conector.text("---------------------------------")
                        conector.text("Son:"+pago+" SOLES")

                        const resp = await conector.imprimir(nombreImpresora, api_key);
                        if (resp === true) {

                             console.log("imprimio: "+resp)
                        } else {
                             console.log("Problema al imprimir: "+resp)

                        }

}



async function enviarTicketera(impresora,datos){
  let conector = new ConectorPluginV3();
  conector
  .Iniciar()
  .EstablecerAlineacion(ConectorPluginV3.ALINEACION_CENTRO)
 .Feed(1)
  .DescargarImagenDeInternetEImprimir("https://aprendeadistancia.online/erp/assets/img/logo-erp-bn.jpg", ConectorPluginV3.TAMAÑO_IMAGEN_NORMAL, 400)
  .Iniciar()
  .Feed(1)
  .EstablecerAlineacion(ConectorPluginV3.ALINEACION_CENTRO)
  .EscribirTexto("LAS HERMANITAS\n")
  .EscribirTexto("Ferretería y materiales de contrucción las\n")
  .EscribirTexto("Hermanitas E.I.R.L.\n")
  .EscribirTexto("Wharsapp/Teléfono:902 715 979 \n")
  .EscribirTexto("lashermanitas_bertha@hotmail.com \n")
  .EscribirTexto("LT. 9 MZ E COO. LA ESPERANZA - Santiago de Surco - Lima - Lima \n")
  .EscribirTexto("RUC: 2053799520\n")
  .Feed(1);
  for (let index = 0; index < datos.length; index++) {
    const element = datos[index].nombre;
    conector.EscribirTexto(element)
  }
   conector
  .CorteParcial()
  const respuesta = await conector.imprimirEn(impresora);
  if (respuesta == true) {
    console.log("Impresión correcta");
  } else {
    console.log("Error: " + respuesta);
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
clientetexto:string;
direccioncliente:string;
numero_doc:string;
textoprecio:any;
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
   /* const conector = new ConectorPluginV3;
    const amongUsComoCadena = `000001111000
    000010000100
    000100011110
    000100100001
    011100100001
    010100100001
    010100100001
    010100011110
    010100000010
    011100000010
    000100111010
    000100101010
    000111101110
    000000000000
    000000000000
    000000000000
    111010101110
    100010101000
    111010101110
    001010100010
    111011101110
    000000000000
    000000000000
    000000000000`;

    conector.Iniciar()

    conector.EstablecerAlineacion(ConectorPluginV3.ALINEACION_CENTRO)
    conector.DescargarImagenDeInternetEImprimir("https://aprendeadistancia.online/erp/assets/img/logo-erp.png", 0, 216)
    conector.Feed(1)
    conector.EscribirTexto("LAS HERMANITAS\n")
    conector.EscribirTexto("Ferretería y materiales de contrucción las\n")
    conector.EscribirTexto("Hermanitas E.I.R.L.\n")
    conector.EscribirTexto("Wharsapp/Teléfono:902 715 979 \n")
    conector.EscribirTexto("lashermanitas_bertha@hotmail.com \n")
    conector.EscribirTexto("LT. 9 MZ E COO. LA ESPERANZA - Santiago de Surco - Lima - Lima \n")
    conector.EscribirTexto("RUC: 2053799520\n")
    conector.EstablecerAlineacion(ConectorPluginV3.ALINEACION_IZQUIERDA)
    //conector.EscribirTexto('CLIENTE:'+this.MyForm.get('cliente').value +'\n')

    conector.EscribirTexto("Fecha y hora: " + (new Intl.DateTimeFormat("es-PE").format(new Date())))
    conector.Feed(1)
    conector.EstablecerAlineacion(ConectorPluginV3.ALINEACION_IZQUIERDA)
    conector.EscribirTexto("_______________________\n")

    conector.EstablecerAlineacion(ConectorPluginV3.ALINEACION_DERECHA);

    this.data.detalle.forEach(element => {
      conector.EscribirTexto(element.nombre+"          "+element.precio+'\n')
      conector.EscribirTexto("_____________________\n")
    });
    conector.EscribirTexto("____________________\n")
    conector.EscribirTexto("\n")
    conector.EscribirTexto("____________________\n")
    conector.EstablecerAlineacion(ConectorPluginV3.ALINEACION_CENTRO)
    conector.HabilitarCaracteresPersonalizados()
    conector.DefinirCaracterPersonalizado("$", amongUsComoCadena)
    //.EscribirTexto("En lugar del simbolo de pesos debe aparecer un among us\n")
    //.EscribirTexto("TOTAL: $25\n")
    conector.EstablecerEnfatizado(true)
    conector.EstablecerTamañoFuente(1, 1)
    conector.TextoSegunPaginaDeCodigos(2, "cp850", "¡Gracias por su compra!\n")
    conector.Feed(1)

    conector.ImprimirCodigoQr("https://parzibyte.me/blog", 160, ConectorPluginV3.RECUPERACION_QR_MEJOR, ConectorPluginV3.TAMAÑO_IMAGEN_NORMAL)
    conector.Feed(1)
    conector.ImprimirCodigoDeBarrasCode128("parzibyte.me", 80, 192, ConectorPluginV3.TAMAÑO_IMAGEN_NORMAL)
    conector.Feed(1)
    conector.EstablecerTamañoFuente(1, 1)
    conector.EscribirTexto("parzibyte.me\n")
    conector.Feed(3)
    conector.Corte(1)
    conector.Pulso(48, 60, 120)
    //.imprimirEn(this.MyForm.get('impresoras').value);
    const respuesta =  conector.imprimirEn(this.MyForm.get('impresoras').value);
    if(respuesta){
      console.log("imprimio correcto")
console.log(respuesta)
    }else{
      console.log("imprimio incorecto")
    }
*/
console.log(this.data.detalle)
console.log(this.MyForm)
const print = this.MyForm.get('imprimir') as FormControl;
let impresora = this.MyForm.get('impresoras') as FormControl;
if(print.value==true){
  console.log("impresoras",impresora.value);
//this.imprimir();
//enviarTicketera(impresora.value,this.data.detalle)
imprimirTicket(this.data.detalle,this.MyForm,this.clientetexto,this.direccioncliente,this.textoprecio)
}
    this.api.guardaVentas(this.MyForm.value,this.data.detalle).subscribe(
      data=>{
        console.log("form",this.MyForm.value)
        console.log("detallesss",this.data.detalle)
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
    const idUsuario = localStorage.getItem("currentId")
    this.api.getCajasUsuario(idUsuario).subscribe(data => {
      if(data) {
        this.dataCajas = data;
      }
    } );
  }

seleccionarCliente(event){
console.log(event.value)
  this.api.getApiTablaCriterio('clientes',event.value).subscribe(data => {
    if(data[0].nombre) {
this.clientetexto=data[0].nombre
this.numero_doc=data[0].num_documento
this.direccioncliente=data[0].direccion
    }
  } );
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
      const neto = this.MyForm.get('neto') as FormControl;
      if(value=='Factura'){
      this.data.detalle.map(function(dato){
          console.log("datoooooo",dato)
        total2.setValue((this.data.precio/Global.EXTRAE_IGV) + ((this.data.precio/Global.EXTRAE_IGV) * Global.BASE_IGV).toFixed(2));
        igv2.setValue((dato.precio/Global.EXTRAE_IGV * Global.BASE_IGV).toFixed(2));
        });
       // total2.setValue(parseFloat(total2.value) + (total2.value * Global.BASE_IGV))
        //igv2.setValue(total2.value * Global.BASE_IGV)
      }
      if(value=='Ticket'){
        total2.setValue(this.data.precio);

      }
      neto.setValue(this.data.precio)
  }

  cambiaVuelto(precio:number){
    var vuelto = this.MyForm.get('vuelto') as FormControl;
  const tipoDoc1 = this.MyForm.get('tipoDoc') as FormControl;
  let efectivo:number=0;
  let depositos=0;
  let total:number=0;
    this.MyForm.value.pagos.forEach(element => {
      console.log(element)
  if(element.tipoPago=="Efectivo" || element.tipoPago=='Yape'){
      efectivo+=element.montoPago;
    }else{
    depositos+=element.montoPago;
    console.log("deposito",depositos)
    }
    });
 total=depositos+efectivo;
 vuelto.setValue(total-precio);
if(depositos>precio){
  var vuelto = this.MyForm.get('vuelto') as FormControl;
 // this._snackBar.open("El monto recibido es mayor al precio total","Aceptar",{verticalPosition:'bottom'});
   vuelto.setValue(total-precio);
}else if(depositos==precio){
  var vuelto = this.MyForm.get('vuelto') as FormControl;
  vuelto.setValue(0.00);

  this.api.getNumeroALetras(precio.toString()).subscribe(letra => {
    console.log("letra",letra)
  this.textoprecio=letra;
  });

}
    if(efectivo<0){
      const vuelto = this.MyForm.get('vuelto') as FormControl;
      this._snackBar.open("El monto recibido no es suficiente","Aceptar",{verticalPosition:'bottom'});
      vuelto.setValue('');
  }else if(efectivo==precio && tipoDoc1.value!="Factura"){
  const vuelto = this.MyForm.get('vuelto') as FormControl;

    this.api.getNumeroALetras(precio.toString()).subscribe(letra => {
      console.log("letra",letra)
    this.textoprecio=letra;
    });

}else if(efectivo<precio && depositos<precio){
  const total3 = this.MyForm.get('total') as FormControl;
  const igv3 = this.MyForm.get('igv') as FormControl;
  const neto3 = this.MyForm.get('neto') as FormControl;
  this.vuelto='Pendiente';
  console.log("pendiente");
  const vuelto = this.MyForm.get('vuelto') as FormControl;
  vuelto.setValue(precio-efectivo)
  const mpendiente = this.MyForm.get('montopendiente') as FormControl;
  mpendiente.setValue(precio-efectivo);
  let precio_p = precio/Global.EXTRAE_IGV
  console.log("precio_p",precio_p);
console.log("igv",(precio_p * Global.BASE_IGV).toFixed(2))
vuelto.setValue((efectivo-(precio_p + (precio_p * Global.BASE_IGV))).toFixed(2));
let total=((precio_p + (precio_p * Global.BASE_IGV))).toFixed(2);
total3.setValue(total);
igv3.setValue((precio_p * Global.BASE_IGV).toFixed(2))
neto3.setValue((precio/Global.EXTRAE_IGV).toFixed(2))


  this.api.getNumeroALetras(precio.toString()).subscribe(letra => {
    console.log("letra",letra)
  this.textoprecio=letra;
  });
}
else if(efectivo==precio || depositos==precio ||efectivo>precio ) {
    const vuelto = this.MyForm.get('vuelto') as FormControl;
    const tipoDoc = this.MyForm.get('tipoDoc') as FormControl;
    const total3 = this.MyForm.get('total') as FormControl;
    const igv3 = this.MyForm.get('igv') as FormControl;
    const neto3 = this.MyForm.get('neto') as FormControl;
    if(tipoDoc.value=="Factura"){
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
    else{
      this.vuelto='Vuelto';
      console.log("else")
      this.api.getNumeroALetras(precio.toString()).subscribe(letra => {
        this.textoprecio=letra;
        });
    // vuelto.setValue(efectivo-precio)
    }
  }



  }
}
