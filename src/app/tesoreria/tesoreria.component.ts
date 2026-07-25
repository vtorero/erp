import { Component, ElementRef, Inject, OnInit, ViewChild } from '@angular/core';
import { ApiService } from 'app/api.service';
import { Movimiento } from 'app/modelos/movimiento';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';


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
  displayedColumns = ['id','fecha_registro','tipo','concepto','monto'];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  @ViewChild('fileInput') fileInput!: ElementRef;
  dataSource: any;
    mov: Movimiento = new Movimiento(
        '',
        1,      // 1 = Ingreso
        '',      // Cuenta
        0,
        '',
        ''
      );

    presupuestoInicial:number=0;
    public selectedMoment = new Date();
  public selectedMoment2 = new Date();
fec1= this.selectedMoment.toDateString().split(" ",4);
fec2 = this.selectedMoment2.toDateString().split(" ",4);
fecha1:string=this.fec1[2]+'-'+this.fec1[1]+'-'+this.fec1[3];
fecha2:string=this.fec2[2]+'-'+this.fec2[1]+'-'+this.fec2[3];
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
        this.renderDataTable(value);
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
        this.renderDataTable(this.mov.cuenta);
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

    enviaFechas(){

      var empresa = sessionStorage.getItem("CurrentUser");
      var fec1 = this.selectedMoment.toDateString().split(" ",4);
      var fec2 = this.selectedMoment2.toDateString().split(" ",4);
      let ini=fec1[1]+fec1[2]+fec1[3];
      let fin=fec2[1]+fec2[2]+fec2[3];

      this.fecha1=fec1[2]+'-'+fec1[1]+'-'+fec1[3];;
      this.fecha2=fec2[2]+'-'+fec2[1]+'-'+fec2[3];;

      console.log(this.fecha1)
      console.log(this.fecha2)

     // this.loadVentas(this.fecha1,this.fecha2,empresa);


      //this.renderDataTableConsulta(ini,fin,empresa);
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

    renderDataTable(cuenta:string) {
      this.api.getSelectApi('movimiento_caja/',cuenta).subscribe(x => {
        this.dataSource = new MatTableDataSource();
        this.dataSource.data = x;
        this.empTbSort.disableClear = true;
        this.dataSource.sort = this.empTbSort;
        this.dataSource.paginator = this.paginator;
        },
        error => {
          console.log('Error de conexion de datatable!' + error);
        });
    }

}