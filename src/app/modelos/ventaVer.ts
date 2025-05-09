import { Details } from './details';
export class VentaVer {
    constructor(
        public id:number,
        public id_usuario:string,
        public id_sucursal:any,
        public telefono:any,
        public direccion:any,
        public id_cliente: any,
        public num_documento:any,
        public cliente:any,
        public	estado:string,
        public nro_comprobante:string,
        public fecha:any,
        public fechaPago:any,
        public igv:number,
        public monto_igv:number,
        public valor_total:number,
        public monto_pendiente:number,
        public detalleVenta:Array<Details>,
        public imprimir:boolean,
        public tipoDoc:string,
        public valor_neto:number,
        public observacion:string,
        public formaPago:string
        ){}
}