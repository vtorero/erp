import { Component, OnInit, ViewChild } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { filter, finalize, map } from 'rxjs';
import { ApiService } from 'app/api.service';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';

@Component({
  selector: 'app-ventas',
  templateUrl: './ventas.component.html',
  styleUrls: ['./ventas.component.css']
})

export class VentasComponent implements OnInit {
  selectedRowIndex:any;
  dataSource: any;
  loading:boolean=false;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild('empTbSort') empTbSort = new MatSort();
  constructor(
    private api: ApiService,
  ) { }

  ngOnInit(): void {
    this.renderDataTable()
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


}
