import { DecimalPipe } from '@angular/common';
export class Details {
    constructor(
        public id: number,
        public codigo:string,
        public nombre: string,
        public cantidad:number,
        public despacho:number,
        public pendiente:number,
        public almacen:number,
        public precio: number,
        public precio_compra:number,
        public descuento: number,
        public detalle:any

){}
}