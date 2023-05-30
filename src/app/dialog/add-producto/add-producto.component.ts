import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Productos } from '../../modelos/producto';

@Component({
  selector: 'app-add-producto',
  templateUrl: './add-producto.component.html',
  styleUrls: ['./add-producto.component.css']
})
export class AddProductoComponent implements OnInit {
dataSource:any;
dataCategoria:any;
dataSubCategoria:any;
dataFamilia:any;
isLoaded:boolean=false
  constructor(
    private toastr: MatSnackBar,
    public dialogRef: MatDialogRef<AddProductoComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Productos,
    private api:ApiService


  ) { }

  ngOnInit(): void {
    this.getCate();
    this.getSubCategoria();
    this.getFamilia();

  }
  getCate(): void {
    this.api.getApi('categorias').subscribe(data => {
      if(data) {
        this.dataCategoria = data;
        console.log(this.dataCategoria)
      }
    } );
  }

  getSubCategoria(): void {
    this.api.getApi('sub_categorias').subscribe(data => {
      if(data) {
        this.dataSubCategoria = data;
        console.log(this.dataCategoria)
      }
    } );
  }

  getFamilia(): void {
    this.api.getApi('familia').subscribe(data => {
      if(data) {
        this.dataFamilia = data;
        console.log(this.dataCategoria)
      }
    } );
  }

}
