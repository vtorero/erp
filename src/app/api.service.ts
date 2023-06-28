import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { map, Observable } from "rxjs";
import { Global } from "./global";
import { Proveedor } from "./modelos/proveedor";
import { Usuario } from "./modelos/usuario";
import { Clientes } from './modelos/clientes';
import { Productos } from './modelos/producto';
import { Categoria } from "./modelos/categoria";

@Injectable({
  providedIn: "root",
})
export class ApiService {
  constructor(public _http: HttpClient) {}
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

  getApi(ruta: string) {
    return this._http
      .get(Global.BASE_API_URL + "api.php/" + ruta, { headers: this.headers })
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

listarUsuarios() {
  return this._http
    .get(Global.BASE_API_URL + "api.php/usuarios", { headers: this.headers })
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

/*producto*/

public GuardarProducto(datos: Productos): Observable<any> {
  let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
  let json = JSON.stringify(datos);
  return this._http.post(Global.BASE_API_URL + 'api.php/producto',
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

public BuscarProducto(criterio:string): Observable<any> {

  //return this._http.get<Categoria[]>(Global.BASE_API_URL + 'api.php/categorias', { headers: this.headers });
  return this._http.get(Global.BASE_API_URL + 'api.php/articulos/' + criterio, { headers: this.headers });
}


/*Apis usuarios*/



  /*dosimetria

  public GuardarDosimetria(datos){
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    let json = JSON.stringify(datos);
    return this._http.post(Global.BASE_API_URL + 'api.php/dosimetria',
      { json: json }, { headers: headers });
  }
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

  GetDetalleVenta(id: any) {
    return this._http.get(Global.BASE_API_URL + 'api.php/venta/' + id,
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


  getNumeroALetras(cantidad:number) {
    return this._http.get(Global.BASE_API_URL+ 'api.php/numeroletras/'+ cantidad,
    { headers: this.headers }
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
