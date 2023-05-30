import { NumberSymbol } from "@angular/common";

export class Productos{
    constructor(
        public codigo: string,
        public nombre: string,
        public unidad: string,
        public descripcion: string,
        public familia:string,
        public id_categoria: number,
        public categoria:string,
        public id_subcategoria: number,
        public subcategoria:string,
        public marca:string,
        public costo:string,
        public precio:string,
        public ISCTypeDesc:string,
        public FlagICBPER:string,
        public FlagLotSerial:string,
        public clase:string
    ){}
}

