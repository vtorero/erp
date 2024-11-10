import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Categoria } from 'app/modelos/categoria';
import { Subcategoria } from 'app/modelos/subcategoria';

@Component({
  selector: 'app-add-sub-categoria',
  templateUrl: './add-sub-categoria.component.html',
  styleUrls: ['./add-sub-categoria.component.css']
})
export class AddSubCategoriaComponent implements OnInit {
  dataCategoria:any;
  constructor(
    private _snackBar: MatSnackBar,
    private api:ApiService,
    @Inject(MAT_DIALOG_DATA) public data: Subcategoria

  ) { }

  ngOnInit(): void {
this.getCate();
  }
  getCate(): void {
    this.api.getApi('categorias').subscribe(data => {
      if(data) {
        console.log("cate",data)
        this.dataCategoria = data;
      }
    } );
  }
}
