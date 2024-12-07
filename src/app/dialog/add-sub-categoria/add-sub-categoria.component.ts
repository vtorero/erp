import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Subcategoria } from 'app/modelos/subcategoria';

@Component({
  selector: 'app-add-sub-categoria',
  templateUrl: './add-sub-categoria.component.html',
  styleUrls: ['./add-sub-categoria.component.css']
})
export class AddSubCategoriaComponent implements OnInit {
  dataCategoria:any;
  constructor(

    private api:ApiService,
    public dialogRef: MatDialogRef<AddSubCategoriaComponent>,
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
  cancelar() {
    this.dialogRef.close();
  }

}
