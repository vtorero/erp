import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Familia } from 'app/modelos/familia';


@Component({
  selector: 'app-add-familia',
  templateUrl: './add-familia.component.html',
  styleUrls: ['./add-familia.component.css']
})
export class AddFamiliaComponent implements OnInit {
  dataCategoria:any;
  dataUnidad:any;
  dataSubCategoria:any;
  constructor(
    private _snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data: Familia,
    private api:ApiService
  ) { }

  ngOnInit(): void {
    this.getCate();
    this.getSubCategoria();
  }


  getCate(): void {
    this.api.getApi('categorias').subscribe(data => {
      if(data) {
        console.log("cate",data)
        this.dataCategoria = data;
      }
    } );
  }

  getSubCategoria(): void {
    this.api.getApi('sub_categorias').subscribe(data => {
      if(data) {
        this.dataSubCategoria = data;
      }
    } );
  }

  public seleccionarCategoria(event) {
    const value = event.value;
    this.api.BuscarPorSubCategoriaCategoria(value).subscribe(x => {
      this.dataSubCategoria=x;

    });

 }
}
