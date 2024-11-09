import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { async, map, Observable } from "rxjs";
import { Global } from "./global";
import { Proveedor } from "./modelos/proveedor";
import { Usuario } from "./modelos/usuario";
import { Clientes } from './modelos/clientes';
import { Productos } from './modelos/producto';
import { Categoria } from "./modelos/categoria";
import { Details } from './modelos/details';
import { Permisos } from './modelos/permisos';
import { Cajas } from './modelos/cajas';
import { AddInventario } from "./modelos/addinventario";
import { Venta } from './modelos/venta';
import {Chart} from 'chart.js/auto';
import { Compra } from "./modelos/compra";




@Injectable({
  providedIn: "root",
})

export class ApiService {

  constructor(public _http: HttpClient) {}
  public articulos=[];
  public chart:Chart;

  headers: HttpHeaders = new HttpHeaders({
    "Content-type": "application/json",
  });
  getSelectApi(tabla: string,criterio:string) {
    return this._http.get(Global.BASE_API_URL + 'api.php/' + tabla+criterio,
      { headers: this.headers }
    ).pipe(map(result => result));
  }
  getMaxId(tabla: string) {
    return this._http
      .get("api.php/correlativo/" + tabla, { headers: this.headers })
      .pipe(map((result) => result));
  }

  get getCurrentUser(){
    let user = sessionStorage.getItem("currentUser");
    if(!user){
      return false;
       }else{
         return true;
       }
  }
  getPie(labels:any,datos:any,canvas:string,titulo:string){

    return this.chart = new Chart(canvas, {
      type: 'doughnut',
      options: {
        plugins: {
          legend: {
            position: 'right',
          },
          title: {
            display: false,
            text: titulo,
            color: 'black',
            font: {
              size: 14,
              family: 'verdana',
              weight: 'normal',
              style: 'normal'
            },
          },
          subtitle: {
            display: false,
            text: 'Periodo seleccionado',
            color: 'black',
            font: {
              size: 12,
              family: 'verdana',
              weight: 'normal',
              style: 'normal'
            },
            padding: {
              bottom: 1
            }

      }
    }
    },
         data: {
        labels: labels,
        datasets: [
          {
          // backgroundColor: "RGBA(0,233,168,0.3)",
          //  borderColor: "#3cb371",
            //borderDash: [],
            borderDashOffset: 0.0,

            data: datos,
           // borderColor: '#3cba9f',
            //fill: true,
           /* backgroundColor: [
              "#0f498aff",
              "#999999ff",
              "#2196f3ff",
              "#ccccccff",
              "#bbdefbff",
              "#f990a7",
              "#aad2ed",
              "#FF00FF",
              "Blue",
              "Red",
              "Blue"
            ]*/
          },


        ],

      },

    }
    )

  }
  getLine(labels:any,datos:any,canvas:string,titulo:string){

    return this.chart = new Chart(canvas, {
      type: 'line',
      options: {
        plugins: {

          title: {
            display: false,
            text: titulo,
            color: 'black',
            font: {
              size: 14,
              family: 'verdana',
              weight: 'normal',
              style: 'normal'
            },
          },
          subtitle: {
            display: false,
            text: 'Periodo seleccionado',
            color: 'black',
            font: {
              size: 12,
              family: 'verdana',
              weight: 'normal',
              style: 'normal'
            },
            padding: {
              bottom: 1
            }

      }
    },



    },
         data: {
        labels: labels,
        datasets: [
          {
            label: 'Días',
          // backgroundColor: "RGBA(0,233,168,0.3)",
          //  borderColor: "#3cb371",
            //borderDash: [],
            borderDashOffset: 0.0,

            data: datos,
           // borderColor: '#3cba9f',
            //fill: true,
           /* backgroundColor: [
              "#0f498aff",
              "#999999ff",
              "#2196f3ff",
              "#ccccccff",
              "#bbdefbff",
              "#f990a7",
              "#aad2ed",
              "#FF00FF",
              "Blue",
              "Red",
              "Blue"
            ]*/
          },


        ],

      },

    }
    )

  }


  getApi(ruta: string) {
    return this._http
      .get(Global.BASE_API_URL + "api.php/" + ruta, { headers: this.headers })
      .pipe(map((result) => result));
  }

  getApiTablaCriterio(tabla:string,criterio:string){
    return this._http
      .get(Global.BASE_API_URL + "api.php/tabla/" + tabla +'/'+ criterio, { headers: this.headers })
      .pipe(map((result) => result));
  }



  apiBuscadorProveedor(criterio:string){
    return this._http
      .get(Global.BASE_API_URL + "api.php/buscarproveedor/" + criterio, { headers: this.headers })
      .pipe(map((result) => result));
  }
  apiBuscadorCliente(criterio:string){
    return this._http
      .get(Global.BASE_API_URL + "api.php/buscarclientes/" + criterio, { headers: this.headers })
      .pipe(map((result) => result));
  }

  apiBuscadorProducto(criterio:string){
    return this._http
      .get(Global.BASE_API_URL + "api.php/buscarproducto/" + criterio, { headers: this.headers })
      .pipe(map((result) => result));
  }

getSucursalUsuario(){

   const usuario = localStorage.getItem("currentId");
   return this._http
   .get(Global.BASE_API_URL + "api.php/sucursalusuario/"+ usuario, { headers: this.headers })
   .pipe(map((result) => result));

}


  getApiTabla(criterio){
    return this._http
      .get(Global.BASE_API_URL + "api.php/tabla" + criterio, { headers: this.headers })
      .pipe(map((result) => result));
  }
/**usuario  */

  loginUser(usuario: string, password: string) {
    const url = Global.BASE_API_URL + 'api.php/login';
    return this._http.post(url,{
        usuario: usuario,
        password: password
    }, { headers: this.headers }).pipe(map(data => data));
}

public getCajasUsuario(usuario: string): Observable<any> {
  return this._http.get(Global.BASE_API_URL + 'api.php/cajas/' + usuario,
    { headers: this.headers }
  ).pipe(map(result => result))

}


listarSucursales() {
  return this._http
    .get(Global.BASE_API_URL + "api.php/sucursales", { headers: this.headers })
    .pipe(map((result) => result));
}


listarUsuarios() {
  return this._http
    .get(Global.BASE_API_URL + "api.php/usuarios", { headers: this.headers })
    .pipe(map((result) => result));
}

listarVendedores() {
  return this._http
    .get(Global.BASE_API_URL + "api.php/vendedores", { headers: this.headers })
    .pipe(map((result) => result));
}

public guardarUsuario(datos: Usuario): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/usuario",
    { json: json },
    { headers: headers }
  );
}

public guardarCategoria(categoria:string): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(categoria);
  return this._http.post(
    Global.BASE_API_URL + "api.php/categoria",
    { json: json },
    { headers: headers }
  );
}

public guardarSubCategoria(categoria:string): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(categoria);
  return this._http.post(
    Global.BASE_API_URL + "api.php/subcategoria",
    { json: json },
    { headers: headers }
  );
}



public guardarVendedor(datos: Usuario): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/vendedor",
    { json: json },
    { headers: headers }
  );
}

public actualizaPendientesCompra(id_venta:number,id_producto:number,id:number,cantidad:number):Observable<any>{
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'id_venta':id_venta,
    'id_producto':id_producto,
    'id':id,
    'cantidad':cantidad,
    'sucursal':localStorage.getItem("id_suc"),
    'usuario':localStorage.getItem("currentId")
  }
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + "api.php/actualiza-pendiente-compra",{ json: json },
    { headers: headers }
  )
}

public actualizaPendientesVenta(id_venta:number,id_producto:number,id:number,pendiente:number,cantidad:number):Observable<any>{
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'id_venta':id_venta,
    'id_producto':id_producto,
    'id':id,
    'cantidad':cantidad,
    'pendiente':pendiente,
    'sucursal':localStorage.getItem("id_suc"),
    'usuario':localStorage.getItem("currentId")
  }
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + "api.php/actualiza-pendiente-venta",{ json: json },
    { headers: headers }
  )
}

public actualizaMontoCompra(id_venta:number,tipoPago:number,numero:string,cuentaPago:number,pendiente:number,monto:number):Observable<any>{
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'id_venta':id_venta,
    'tipo_pago':tipoPago,
    'numero':numero,
    'cuenta_pago':cuentaPago,
    'pendiente':pendiente,
    'monto':monto,
    'sucursal':sessionStorage.getItem("id_suc"),
    'usuario':localStorage.getItem("currentId")
  }
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + "api.php/actualiza-monto-compra",{ json: json },
    { headers: headers }
  )
}

public consultaCompras(finicio:string,ffin:string,estado:string):Observable<any>{
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'ini':finicio,
    'fin':ffin,
    'estado':estado
  }
  let json = JSON.stringify(datos);

  return this._http.post(Global.BASE_API_URL + "api.php/consulta-compras",{ json: json },
  { headers: headers });
}


public consultaVentas(finicio:string,ffin:string,estado:string):Observable<any>{
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'ini':finicio,
    'fin':ffin,
    'estado':estado
  }
  let json = JSON.stringify(datos);

  return this._http.post(Global.BASE_API_URL + "api.php/consulta-ventas",{ json: json },
  { headers: headers });
}



public actualizaMonto(id_venta:number,tipoPago:number,numero:string,cuentaPago:number,pendiente:number,monto:number):Observable<any>{
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'id_venta':id_venta,
    'tipo_pago':tipoPago,
    'numero':numero,
    'cuenta_pago':cuentaPago,
    'pendiente':pendiente,
    'monto':monto,
    'sucursal':sessionStorage.getItem("id_suc"),
    'usuario':localStorage.getItem("currentId")
  }
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + "api.php/actualiza-monto",{ json: json },
    { headers: headers }
  )
}


public guardarCajas(datos:Cajas): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/cajas",
    { json: json },
    { headers: headers }
  );
}


public guardarPermisos(datos:Permisos): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/permisos",
    { json: json },
    { headers: headers }
  );
}

getInventarios(criterio:string){
  return this._http
    .get(Global.BASE_API_URL + "api.php/inventario/"+ criterio, { headers: this.headers })
    .pipe(map((result) => result));
}


getPermisos(criterio:string){
  return this._http
    .get(Global.BASE_API_URL + "api.php/permisos/"+ criterio, { headers: this.headers })
    .pipe(map((result) => result));
}



public actualizarUsuario(datos: Usuario): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.put(
    Global.BASE_API_URL + "api.php/usuario",
    { json: json },
    { headers: headers }
  );
}

public eliminarUsuario(datos: Usuario): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/usuario_del",
    { json: json },
    { headers: headers }
  );
}


/** proveedor */
getProveedor(ruc: string) {
  return this._http.get(Global.BASE_API_SUNAT + 'ruc/' + ruc + '?token=' + Global.TOKEN_API_PERU,
    { headers: this.headers }
  ).pipe(map(result => result));
}



public EditarProveedor(datos: Proveedor): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.put(Global.BASE_API_URL + 'api.php/proveedor',
    { json: json }, { headers: headers });
}

public GuardarProveedor(datos: Proveedor): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/proveedor',
    { json: json }, { headers: headers });
}

public delProveedor(datos: Proveedor): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/del_proveedor",
    { json: json },
    { headers: headers }
  );
}

public upd_proveedor(datos: Proveedor): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.put(
    Global.BASE_API_URL + "api.php/proveedor",
    { json: json },
    { headers: headers }
  );
}

/**cliente */

public EditarCliente(datos: Clientes): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.put(Global.BASE_API_URL + 'api.php/cliente',
    { json: json }, { headers: headers });
}

public GuardarCliente(datos: Clientes): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/cliente',
    { json: json }, { headers: headers });
}


public facturaVenta(datos:Venta): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/facturar",
    { json: json },
    { headers: headers }
  );
}
public anularVenta(datos:Venta): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/anular",
    { json: json },
    { headers: headers }
  );
}

public delCliente(datos:Clientes): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/del_cliente",
    { json: json },
    { headers: headers }
  );
}

/**eliminar compra */
public delCompra(datos:Compra): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/del_compra",
    { json: json },
    { headers: headers }
  );
}


/*buscar producto*/

public BuscarKardex(prod:string,sucursal:string,movimiento:string,inicio:string,fin:string,compra:number,venta:number): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let datos = {
    'producto':prod,
    'sucursal':sucursal,
    'movimiento':movimiento,
    'inicio':inicio,
    'fin':fin,
    'compra':compra,
    'venta':venta
 }
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/kardex',
    { json: json }, { headers: headers });

}


/*producto*/

public GuardarProducto(datos: Productos): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/producto',
    { json: json }, { headers: headers });
}

public AgregarInventario(datos: AddInventario): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/agregar-inventario',
    { json: json }, { headers: headers });
}


getCategoriaSelect(): Observable<Categoria[]> {
  return this._http.get<Categoria[]>(Global.BASE_API_URL + 'api.php/categorias', { headers: this.headers });
}

public EditarProducto(datos: Productos): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.put(Global.BASE_API_URL + 'api.php/producto',
    { json: json }, { headers: headers });
}

getProductosSelect(value = ''): Observable<Productos[]> {
  if (value == '') {
    return this._http.get<Productos[]>(Global.BASE_API_URL + 'api.php/articulos', { headers: this.headers });
  } else {
    return this._http.get<Productos[]>(Global.BASE_API_URL + 'api.php/articulos/' + value, { headers: this.headers });
  }
}

public delProducto(datos: Productos): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let json = JSON.stringify(datos);
  return this._http.post(
    Global.BASE_API_URL + "api.php/del_producto",
    { json: json },
    { headers: headers }
  );
}

public BuscarProducto(datos:string): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/buscaarticulos',
    { json: json }, { headers: headers });
}

//*Buscar x Categoria*/
public BuscarPorCategoria(id:string): Observable<any> {
  return this._http.get(Global.BASE_API_URL + 'api.php/subcategoria/' +id, { headers: this.headers });
}

/**buscar x familia */

public BuscarPorSubcategoria(id:string): Observable<any> {
  return this._http.get(Global.BASE_API_URL + 'api.php/familia/' +id, { headers: this.headers });
}

public BuscarPorFamilia(cat:string,subcat:string,fam:string,tipo:string): Observable<any> {
  let headers = new HttpHeaders().set(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  let datos = {
    'cat':cat,
    'sub':subcat,
    'fam':fam,
    'tipo':tipo
 }
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + "api.php/buscargeneral",{ json },
    { headers: headers }
  )


}



addLinea(elemento:Details){
this.articulos.push(elemento);
return this.getLinea()
}

getLinea(){
  return this.articulos;
}

/*Apis usuarios*/

public guardaVentas(datos,detalle){
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  let det = JSON.stringify(detalle);
  return this._http.post(Global.BASE_API_URL + 'api.php/venta',
    { json:json,detalle:det }, { headers: headers });
}

/**Guardar Compras */

public guardarCompras(datos,detalle){
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  let det = JSON.stringify(detalle);
  return this._http.post(Global.BASE_API_URL + 'api.php/compra',
    { json:json,detalle:det }, { headers: headers });
}

public GetDetalleCompra(id: any) {
  return this._http.get(Global.BASE_API_URL + 'api.php/compra/' + id,
    { headers: this.headers }
  ).pipe(map(result => result));
}


public GetDetalleVenta(id: any) {
  return this._http.get(Global.BASE_API_URL + 'api.php/venta/' + id,
    { headers: this.headers }
  ).pipe(map(result => result));
}
public GetDetallePagoCompra(id: any) {
  return this._http.get(Global.BASE_API_URL + 'api.php/pagos-compra/' + id,
    { headers: this.headers }
  ).pipe(map(result => result));
}

public GetDetallePago(id: any) {
  return this._http.get(Global.BASE_API_URL + 'api.php/pagos/' + id,
    { headers: this.headers }
  ).pipe(map(result => result));
}
public GetDetalleMovimiento(id: any) {
  return this._http.get(Global.BASE_API_URL + 'api.php/movimiento/' + id,
    { headers: this.headers }
  ).pipe(map(result => result));
}

 getNumeroALetras(cantidad:string) {
  return  this._http.get(Global.BASE_API_URL+ 'api.php/numeroletras/'+ cantidad,
  { headers: this.headers }
).pipe(map(result => result));
}

getVentaBoletas(inicio: string, final: string, empresa: string) {
  const url = Global.BASE_API_URL + 'reportes.php/reporte';
  return this._http.post(url, {
    ini: inicio,
    fin: final,
    emp: empresa
  }, { headers: this.headers }).pipe(map(data => data));
}

/**


  /*dosimetria


  public GuardarDosimetriaMov(datos){
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/dosimetriamov',
      { json: json }, { headers: headers });
  }

  public GuardarComprobante(Boleta):Observable<any>{
    let headers = new HttpHeaders()
    .set('Content-Type', 'application/json')
    .set('Authorization', `Bearer ${Global.TOKEN_FACTURACION}`);
    return this._http.post('https://facturacion.apisperu.com/api/v1/invoice/send',JSON.stringify(Boleta),{ headers: headers });
  }

  public GuardarFactura(datos):Observable<any>{
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/factura',
      { json: json }, { headers: headers });
  }

  public GuardarBoleta(datos):Observable<any>{
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/boleta',
      { json: json }, { headers: headers });
  }

/*
  public GuardarComprobante(Boleta):Observable<any>{
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(Boleta);
    return this._http.post(Global.BASE_API_URL + 'api.php/comprobante',
      { json: json }, { headers: headers });
  }

  public EliminarProducto(datos: Producto): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/productodel',
      { json: json }, { headers: headers });
  }

  public EliminarAlmacen(dato): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this._http.delete(Global.BASE_API_URL + 'api.php/inventario/'+dato, { headers: headers });
  }


  public EliminarDosimetria(dato):Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this._http.delete(Global.BASE_API_URL + 'api.php/dosimetria/'+dato,{ headers: headers });
  }

  public EditarCliente(datos: Clientes): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.put(Global.BASE_API_URL + 'api.php/cliente',
      { json: json }, { headers: headers });
  }

  public EditarProducto(datos: Producto): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/productoedit',
      { json: json }, { headers: headers });
  }


  getCategorias() {
    return this._http.get(Global.BASE_API_URL + 'api.php/categorias',
      { headers: this.headers }
    ).pipe(map(result => result));
  }

  getCategoriaSelect(): Observable<Categoria[]> {
    return this._http.get<Categoria[]>(Global.BASE_API_URL + 'api.php/categorias', { headers: this.headers });
  }

  getClienteVenta(id:number):Observable<Clientes[]> {
    return this._http.get<Clientes[]>(Global.BASE_API_URL + 'api.php/cliente/'+id, { headers: this.headers });
  }

  getProveedorSelect(value = ''): Observable<Proveedor[]> {
    if (value == '') {
      return this._http.get<Proveedor[]>(Global.BASE_API_URL + 'api.php/proveedores', { headers: this.headers });
    } else {
      return this._http.get<Proveedor[]>(Global.BASE_API_URL + 'api.php/proveedores/' + value, { headers: this.headers });
    }
  }

  getProductosSelect(value = ''): Observable<Producto[]> {
    if (value == '') {
      return this._http.get<Producto[]>(Global.BASE_API_URL + 'api.php/productos', { headers: this.headers });
    } else {
      return this._http.get<Producto[]>(Global.BASE_API_URL + 'api.php/productos/' + value, { headers: this.headers });
    }
  }

  getSelectApi(tabla: string,criterio:string) {
    return this._http.get(Global.BASE_API_URL + 'api.php/' + tabla+criterio,
      { headers: this.headers }
    ).pipe(map(result => result));
  }


  getAvisosInventarios(): Observable<Avisos[]> {
    return this._http.get<Avisos[]>(Global.BASE_API_URL + 'api.php/alertaintentario', { headers: this.headers });

  }

  public GuardarCategoria(datos: Categoria): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/categoria',
      { json: json }, { headers: headers });
  }
  public GuardarSubCategoria(datos: Subcategoria): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/subcategoria',
      { json: json }, { headers: headers });
  }
/*vendedores



  public GuardarVendedor(datos: Vendedor): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/vendedores',
      { json: json }, { headers: headers });
  }
  public EliminarVendedor(id: any): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this._http.delete(Global.BASE_API_URL + 'api.php/vendedores/'+id,{headers:headers});
  }

  public EliminarCategoria(datos: Categoria): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/categoriadel',
      { json: json }, { headers: headers });
  }


  /*PROVEEDORES

  getProveedores() {
    return this._http.get(Global.BASE_API_URL + 'api.php/proveedores',
      { headers: this.headers }
    ).pipe(map(result => result));
  }

  public GuardarProveedor(datos: Proveedor): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/proveedor',
      { json: json }, { headers: headers });
  }

  public EliminarProveedor(id:number): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this._http.delete(Global.BASE_API_URL + 'api.php/proveedor/'+id,{headers:headers});
  }

  public EliminarEmpresa(id:number): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this._http.delete(Global.BASE_API_URL + 'api.php/empresa/'+id,{headers:headers});
  }

  public EliminarCliente(id:string): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this._http.delete(Global.BASE_API_URL + 'api.php/cliente/'+id,{headers:headers});
  }

  /**Compras  api


  GuardarCompra(datos: Compra): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/compra',
      { json: json }, { headers: headers });
  }

  /*ventas

  GuardarVenta(datos:Venta): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/venta',
      { json: json }, { headers: headers });
  }

  /*inventario

  GuardarInventario(datos: Inventario): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/inventario',
      { json: json }, { headers: headers });
  }
  EditarInventario(datos: Inventario): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.put(Global.BASE_API_URL + 'api.php/inventario',
      { json: json }, { headers: headers });
  }

  EditarCompra(datos: Compra): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/compraedit',
      { json: json }, { headers: headers });
  }

/* Facturacion

enviaFactura(id): Observable<any> {
  return this._http.post(Global.BASE_API_SUNAT + 'api/compacto/',{headers: {Authorization:"Bearer "+ Global.TOKEN_API_PERU_BEARER}});
}


  GetDetalleCompra(id: any) {
    return this._http.get(Global.BASE_API_URL + 'api.php/compra/' + id,
      { headers: this.headers }
    ).pipe(map(result => result));
  }



  getReportes(inicio: string, final: string, empresa: string) {
    const url = Global.BASE_API_URL + 'api.php/reporte';
    return this._http.post(url, {
      ini: inicio,
      fin: final,
      emp: empresa
    }, { headers: this.headers }).pipe(map(data => data));
  }

  getProveedor(ruc: string) {
    return this._http.get(Global.BASE_API_SUNAT + 'ruc/' + ruc + '?token=' + Global.TOKEN_API_PERU,
      { headers: this.headers }
    ).pipe(map(result => result));
  }


  getCliente(dni: string) {
    return this._http.get(Global.BASE_API_SUNAT + 'dni/' + dni + '?token=' + Global.TOKEN_API_PERU,
      { headers: this.headers }
    ).pipe(map(result => result));
  }

  GuardarCliente(datos: Clientes): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/cliente',
      { json: json }, { headers: headers });
  }

  public GuardarEmpresa(datos: Proveedor): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/empresa',
      { json: json }, { headers: headers });
  }

  EditarEmpresa(datos:Proveedor): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.put(Global.BASE_API_URL + 'api.php/empresa',
      { json: json }, { headers: headers });
  }

  getDatos(empresa: string) {
    return this._http.post(Global.BASE_API_URL + 'api.php/inicio',
      {
        emp: empresa
      }, { headers: this.headers }
    ).pipe(map(result => result));
  }




  public GuardarDataBanco(datos: Databanco): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/banco',
      { json: json }, { headers: headers });
  }

  public GuardarDatosGeneral(datos: Datosgeneral): Observable<any> {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/general',
      { json: json }, { headers: headers });
  }

  getDatosBanco(empresa: string) {
    return this._http.post(Global.BASE_API_URL + 'api.php/bancosget',
      {
        empresa: empresa
      }, { headers: this.headers }
    ).pipe(map(result => result));
  }

  getDatosGeneral(empresa: string) {
    return this._http.post(Global.BASE_API_URL + 'api.php/generalget',
      {
        empresa: empresa
      }, { headers: this.headers }
    ).pipe(map(result => result));
  }


  public getTablaInicial(empresa: string): Observable<Impresiones[]> {
    return this._http.post<Impresiones[]>(Global.BASE_API_URL + 'api.php/tabla', {
      emp: empresa
    }, { headers: this.headers }).pipe(map(result => result));
  }


  public getTablaConsultar(ini: string, fin: string, empresa: string): Observable<Impresiones[]> {
    return this._http.post<Impresiones[]>(Global.BASE_API_URL + 'api.php/tablaconsulta', {
      ini: ini,
      fin: fin,
      emp: empresa
    }, { headers: this.headers }).pipe(map(result => result));
  }


  getPie(labels: any, datos: any, canvas: string, titulo: string) {

    return new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [
          {
            fill: true,
            lineTension: 0,
            //backgroundColor: "RGBA(0,233,168,0.3)",
            //borderColor: "#3cb371",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "3cb371",
            pointBackgroundColor: "3cb371",
            pointBorderWidth: 0,
            pointHoverRadius: 8,
            pointHoverBackgroundColor: "#3cb371",
            pointHoverBorderColor: "3cb371",
            pointHoverBorderWidth: 2,
            pointRadius: 4,
            pointHitRadius: 10,
            data: datos,
            //borderColor: '#3cba9f',
            //fill: true,
            backgroundColor: [
              "#0f498aff",
              "#999999ff",
              "#2196f3ff",
              "#ccccccff",
              "#bbdefbff",
              "#f990a7",
              "#aad2ed",
              "#FF00FF",
              "Blue",
              "Red",
              "Blue"
            ]
          }

        ],

      },
      options: {
        legend: {
          display: true,
          position: 'right',
          labels: {
            fontColor: 'rgb(0,0,0)',
            boxWidth: 10,
            padding: 20,
            fontSize: 10
          }

        },
        responsive: true,
        title: {
          display: true,
          text: titulo,
          fontSize: 14
        },
        tooltips: {
          mode: 'index',
          intersect: true
        },
        hover: {
          mode: 'nearest',
          intersect: true
        },

        scales: {
          xAxes: [],
          yAxes: []
        }
      }
    }
    )

  }*/
}
