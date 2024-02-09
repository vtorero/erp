import { Details } from './details';
export class Compra {
    constructor(
        public id:number,
        public id_usuario:string,
        public id_proveedor:any,
        public id_sucursal:any,
        public cliente:any,
        public	estado:number,
        public serie_documento:string,
        public nro_documento:string,
        public fecha:any,
        public fechaPago:any,
        public igv:number,
        public monto_igv:number,
        public monto_pendiente:number,
        public valor_total:number,
        public detalleVenta:Array<Details>,
        public imprimir:boolean,
        public tipoDoc:string,
        public valor_neto:number,
        public observacion:string,
        public formaPago:string
        ){}
}