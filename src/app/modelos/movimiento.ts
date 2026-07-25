export class Movimiento{
    constructor(
        public fecha:string,
        public tipo:number,
        public cuenta:string,
        public monto:number,
        public concepto:string,
         public usuario:string,

    ){}
}
