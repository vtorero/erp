import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Categoria } from 'app/modelos/categoria';
import { DetaPagos } from 'app/modelos/detapagos';

@Component({
  selector: 'app-add-categoria',
  templateUrl: './add-categoria.component.html',
  styleUrls: ['./add-categoria.component.css']
})
export class AddCategoriaComponent implements OnInit {
dataCategoria:any;
  constructor(
    private api:ApiService,
    public dialogRef: MatDialogRef<AddCategoriaComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Categoria

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
