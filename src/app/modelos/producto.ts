export class Producto {
    constructor(
        public codigo: string,
        public nombre: string,
        public unidad: string,
        public descripcion: string,
        public familia:string,
        public categoria:string,
        public subcategoria:string,
        public marca:string,
        public costo:string,
        public precio:string,
        public ISCTypeDesc:string,
        public FlagICBPER:string,
        public FlagLotSerial:string,
    ){}
}

