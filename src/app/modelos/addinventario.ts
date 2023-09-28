export class AddInventario{
    constructor(
        public id_producto :number,
        public cantidad:number,
        public precio:number,
         public usuario:string,
        public id_sucursal:string
    ){}
}
