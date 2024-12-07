export class Productos{
    constructor(
        public id :string,
        public codigo: string,
        public codigobarras: string,
        public nombre: string,
        public unidad: string,
        public descripcion: string,
        public id_familia:string,
        public familia:string,
        public id_categoria: number,
        public categoria:string,
        public id_subcategoria: number,
        public marca:string,
        public costo:string,
        public precio:string,
        public clase:string,
        public imagen:string,
        public nombre_imagen:string
    ){}
}

