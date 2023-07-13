import { Component,  OnInit, ViewChild } from '@angular/core';
import { ApiService } from 'app/api.service';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { Details } from '../../modelos/details';






@Component({
  selector: 'app-ventas',
  templateUrl: './ventas.component.html',
  styleUrls: ['./ventas.component.css']
})



export class VentasComponent implements OnInit {



  selectedRowIndex:any;
  dataSource: any;
  dataTabla:any;
  dataToDisplay = [];
  loading:boolean=false;
  dataRecibo:Array<Details>;
  exampleArray: any[] = [];
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  displayedColumns = ['id', 'nombre','precio'];

  constructor(

    private api: ApiService,

  ) { }

  ngOnInit(): void {
    this.renderDataTable()
    this.dataTabla = this.api.getLinea();
  }



renderDataTable() {
  this.selectedRowIndex=null
  this.api.getApi('articulos').subscribe(x => {
    //this.dataSource = new MatTableDataSource();
     this.dataSource = x;
/*   this.empTbSort.disableClear = true;
    this.dataSource.sort = this.empTbSort;
    this.dataSource.paginator = this.paginator;
    */
    },
    error => {
      console.log('Error de conexion de datatable!' + error);
    });
}

applyFilter(filterValue: string) {
  console.log(filterValue)
  this.loading=true;
  filterValue = filterValue.trim();
  filterValue = filterValue.toLowerCase();
  if(filterValue!=''){
  this.api.BuscarProducto(filterValue).subscribe(x => {
    this.dataSource=x;
    this.loading=false

  }
  )
}else{
  this.renderDataTable()
  this.loading=false
}

}


enviarProducto(id:string,nombre:string,precio:number){
const ele = new Details(id,nombre,precio)
  this.api.addLinea(ele)
  this.dataTabla = this.api.getLinea();
  console.log(this.dataTabla)
}


}
