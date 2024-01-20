export class Kardex{
    constructor(
        public producto:string,
        public id_sucursal: string,
        public movimiento: string,
        public inicio :string,
        public fin:string,
        public id_venta: number,
        public id_compra:number,
    ){}
}