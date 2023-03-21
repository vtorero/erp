import { Injectable } from '@angular/core';


@Injectable({
  providedIn: 'root'
})
export class ServicesService {

  constructor() {}
  public ruta_a_guardar:string;
  public rutas=[];

  add(ruta:string): Array<string> {
    var a = this.rutas.lastIndexOf(ruta);
    console.log("",a);
    if (this.rutas.lastIndexOf(ruta) == -1) {
      console.log("push service:"+ruta)
     this.rutas.push(ruta);
    }
    return this.getRutas();

  }

  del(index:number){
    this.rutas.splice(index,1);
    return this.getRutas();
  }
  getRutas() {
        return this.rutas;
  }
}
