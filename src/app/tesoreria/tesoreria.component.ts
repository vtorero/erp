import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from 'app/api.service';

interface MovimientoTesoreria{

    fecha:string;
    tipo:string;
    concepto:string;
    monto:number;
    observacion:string;

}



@Component({
    selector: 'app-tesoreria',
    templateUrl: './tesoreria.component.html',
    styleUrls: ['./tesoreria.component.css']
  })


export class TesoreriaComponent implements OnInit{


    presupuestoInicial:number=0;
    dataCajas:any;
    movimientos:MovimientoTesoreria[]=[];
    movimiento:MovimientoTesoreria={
        fecha:new Date().toISOString().substring(0,10),
        tipo:'Ingreso',
        concepto:'',
        monto:0,
        observacion:''

    };

    constructor(

         private api:ApiService,

         ) { }
         ngOnInit(): void {
            this.getCajas();
         }

    getCajas(): void {
        let usuario = localStorage.getItem("currentId");
        this.api.getCajasUsuario(usuario).subscribe(data => {
          if(data) {
            this.dataCajas = data;
          }
        } );
      }

      seleccionarCuenta(cuenta:any){
        const value = cuenta.value;
        console.log(value);
        this.api.consultaCuenta(value).subscribe(x => {
            console.log(x);
          });
}

    guardar(){

        if(this.movimiento.concepto.trim()=='' || this.movimiento.monto<=0){
            alert("Complete la información");
            return;
        }

        this.movimientos.unshift({...this.movimiento});

        this.movimiento={
            fecha:new Date().toISOString().substring(0,10),
            tipo:'Ingreso',
            concepto:'',
            monto:0,
            observacion:''
        };

    }

    eliminar(i:number){

        if(confirm("¿Eliminar movimiento?"))
            this.movimientos.splice(i,1);

    }

    get totalIngresos(){

        return this.movimientos
        .filter(x=>x.tipo=="Ingreso")
        .reduce((a,b)=>a+b.monto,0);

    }

    get totalEgresos(){

        return this.movimientos
        .filter(x=>x.tipo=="Egreso")
        .reduce((a,b)=>a+b.monto,0);

    }

    get saldo(){

        return this.presupuestoInicial+
        this.totalIngresos-
        this.totalEgresos;

    }

}