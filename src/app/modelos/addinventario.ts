export class AddInventario{
    constructor(
        public id_producto :number,
        public cantidad:number,
        public precio:number,
         public usuario:string,
         public operacion:string,
         public id_almacen:number,
         public comentario:string,
        public id_sucursal:string
    ){}
}
