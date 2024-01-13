import { Component, Inject, OnInit, ViewChild } from '@angular/core';
import { MAT_DATE_LOCALE } from '@angular/material/core';
import { ApiService } from 'app/api.service';
import * as Chartist from 'chartist';
import { Chart } from 'chart.js/auto';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  public selectedMoment = new Date();
public selectedMoment2 = new Date();
ventaTotal:any;
montoPendiente:any;
montoCompras:any;
dataSource: any;
displayedColumns = ['fecha_registro','Ingreso','usuario','sucursal','responsable','tipopago','monto'];
fec1= this.selectedMoment.toDateString().split(" ",4);
fec2 = this.selectedMoment2.toDateString().split(" ",4);
fecha1:string=this.fec1[2]+'-'+this.fec1[1]+'-'+this.fec1[3];
fecha2:string=this.fec2[2]+'-'+this.fec2[1]+'-'+this.fec2[3];
@ViewChild(MatPaginator) paginator: MatPaginator;
@ViewChild('empTbSort') empTbSort = new MatSort();
  constructor(
    @Inject(MAT_DATE_LOCALE) private _locale: string,
     private api: ApiService
  ) {


   }



  startAnimationForLineChart(chart){
      let seq: any, delays: any, durations: any;
      seq = 0;
      delays = 80;
      durations = 500;

      chart.on('draw', function(data) {
        if(data.type === 'line' || data.type === 'area') {
          data.element.animate({
            d: {
              begin: 600,
              dur: 700,
              from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
              to: data.path.clone().stringify(),
              easing: Chartist.Svg.Easing.easeOutQuint
            }
          });
        } else if(data.type === 'point') {
              seq++;
              data.element.animate({
                opacity: {
                  begin: seq * delays,
                  dur: durations,
                  from: 0,
                  to: 1,
                  easing: 'ease'
                }
              });
          }
      });

      seq = 0;
  };
  startAnimationForBarChart(chart){
      let seq2: any, delays2: any, durations2: any;

      seq2 = 0;
      delays2 = 80;
      durations2 = 500;
      chart.on('draw', function(data) {
        if(data.type === 'bar'){
            seq2++;
            data.element.animate({
              opacity: {
                begin: seq2 * delays2,
                dur: durations2,
                from: 0,
                to: 1,
                easing: 'ease'
              }
            });
        }
      });

      seq2 = 0;
  };

  enviaFechas(){

    var empresa = sessionStorage.getItem("CurrentUser");
    var fec1 = this.selectedMoment.toDateString().split(" ",4);
    var fec2 = this.selectedMoment2.toDateString().split(" ",4);
    let ini=fec1[1]+fec1[2]+fec1[3];
    let fin=fec2[1]+fec2[2]+fec2[3];

    this.fecha1=fec1[2]+'-'+fec1[1]+'-'+fec1[3];;
    this.fecha2=fec2[2]+'-'+fec2[1]+'-'+fec2[3];;

    console.log(this.fecha1,this.fecha2);
    this.loadVentas(this.fecha1,this.fecha2,empresa);


    //this.renderDataTableConsulta(ini,fin,empresa);
    }


    loadVentas(inicio:string,final:string,empresa:string){
      this.api.getVentaBoletas(inicio,final,empresa)
      .subscribe(data => {

        this.dataSource = new MatTableDataSource();
        this.dataSource.data = data['reporte'];
        this.empTbSort.disableClear = true;
        this.dataSource.sort = this.empTbSort;
        this.dataSource.paginator = this.paginator;





        this.ventaTotal=data['boletas'][0].total;
        this.montoPendiente=data['pendiente'][0].pendiente;
        this.montoCompras = data['gasto'][0].gasto;
        let productos_name = data['productos'].map(res=>res.nombre);
        let productos_total = data['productos'].map(res=>res.total);

        let clientes_name = data['clientes'].map(res=>res.nombre);
        let clientes_total = data['clientes'].map(res=>res.total);

        let sucursal_name = data['sucursales'].map(res=>res.nombre);
        let sucursal_total = data['sucursales'].map(res=>res.total);

        let compras_fecha = data['compras'].map(res=>res.fecha);
        let compras_gasto = data['compras'].map(res=>res.gasto);

        let ventas_fecha = data['ventas'].map(res=>res.fecha);
        let ventas_venta = data['ventas'].map(res=>res.venta);


        //grafico productos
        this.api.getPie(productos_name ,productos_total,'canvas','Productos más pedidos');
        //grafico clientes
        this.api.getPie(clientes_name,clientes_total,'canvas2','Clientes');
        //Graficos por sucursal
        this.api.getPie(sucursal_name,sucursal_total,'canvas3','Clientes');
        //Graficos Compras
        this.api.getLine(compras_fecha,compras_gasto,'canvas4','Clientes');
        //Graficos ventas
        this.api.getLine(ventas_fecha,ventas_venta,'canvas5','Clientes');

      });

    }

  ngOnInit() {
      /* ----------==========     Daily Sales Chart initialization For Documentation    ==========---------- */

      const dataDailySalesChart: any = {
          labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
          series: [
              [12, 17, 7, 17, 23, 18, 38]
          ]
      };

     const optionsDailySalesChart: any = {
          lineSmooth: Chartist.Interpolation.cardinal({
              tension: 0
          }),
          low: 0,
          high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
          chartPadding: { top: 0, right: 0, bottom: 0, left: 0},
      }

    //  var dailySalesChart = new Chartist.Line('#dailySalesChart', dataDailySalesChart, optionsDailySalesChart);

      //this.startAnimationForLineChart(dailySalesChart);


      /* ----------==========     Completed Tasks Chart initialization    ==========---------- */

      const dataCompletedTasksChart: any = {
          labels: ['12p', '3p', '6p', '9p', '12p', '3a', '6a', '9a'],
          series: [
              [230, 750, 450, 300, 280, 240, 200, 190]
          ]
      };

     const optionsCompletedTasksChart: any = {
          lineSmooth: Chartist.Interpolation.cardinal({
              tension: 0
          }),
          low: 0,
          high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
          chartPadding: { top: 0, right: 0, bottom: 0, left: 0}
      }

     // var completedTasksChart = new Chartist.Line('#completedTasksChart', dataCompletedTasksChart, optionsCompletedTasksChart);

      // start animation for the Completed Tasks Chart - Line Chart
      //this.startAnimationForLineChart(completedTasksChart);



      /* ----------==========     Emails Subscription Chart initialization    ==========---------- */

      var datawebsiteViewsChart = {
        labels: ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'],
        series: [
          [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895]

        ]
      };
      var optionswebsiteViewsChart = {
          axisX: {
              showGrid: false
          },
          low: 0,
          high: 1000,
          chartPadding: { top: 0, right: 5, bottom: 0, left: 0}
      };
      var responsiveOptions: any[] = [
        ['screen and (max-width: 640px)', {
          seriesBarDistance: 5,
          axisX: {
            labelInterpolationFnc: function (value) {
              return value[0];
            }
          }
        }]
      ];
      var websiteViewsChart = new Chartist.Pie('#websiteViewsChart', datawebsiteViewsChart, optionswebsiteViewsChart, responsiveOptions);

      //start animation for the Emails Subscription Chart
      this.startAnimationForBarChart(websiteViewsChart);
  }

}
