export class DetaPagos {
    constructor(
        public id: number,
        public nombre: string,
        public tipoPago: string,
        public cuentaPago:string,
        public monto:number,
        public monto_pendiente:number,
        public fecha_registro:string,

){}
}