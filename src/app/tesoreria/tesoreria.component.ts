import { Component, Inject, OnInit } from '@angular/core';
import { ApiService } from 'app/api.service';
import { Movimiento } from 'app/modelos/movimiento';
import { MatSnackBar } from '@angular/material/snack-bar';


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

    mov: Movimiento = new Movimiento(
        '',
        1,      // 1 = Ingreso
        0,      // Cuenta
        0,
        '',
        ''
      );

    presupuestoInicial:number=0;
    dataCajas:any;
    totalIngreso:any=0.00;
    totalEgreso:any=0.00;
    totalSaldo:any=0.00;
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
         private _snackBar: MatSnackBar,

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
            this.api.consultaCuenta(value).subscribe((data: any)  => {
            this.totalIngreso= Number(data.ingresos[0].total);
            this.totalEgreso=Number(data.egresos[0].total);
            this.totalSaldo=this.totalIngreso-this.totalEgreso;
          });
}

    guardar(){

        if(this.mov.concepto.trim()=='' || this.mov.monto<=0){
             this._snackBar.open("Complete la información",'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            return;
        }
        console.log(this.mov)
      this.api.guardarMovimiento(this.mov).subscribe(
        data=>{
          this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
          },
        erro=>{console.log(erro)}
          );
        //this.renderDataTable();
    }
        /*this.movimientos.unshift({...this.movimiento});

        this.movimiento={
            fecha:new Date().toISOString().substring(0,10),
            tipo:'Ingreso',
            concepto:'',
            monto:0,
            observacion:''
        };
*/


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