import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Categoria } from 'app/modelos/categoria';

@Component({
  selector: 'app-add-sub-categoria',
  templateUrl: './add-sub-categoria.component.html',
  styleUrls: ['./add-sub-categoria.component.css']
})
export class AddSubCategoriaComponent implements OnInit {

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: Categoria

  ) { }

  ngOnInit(): void {

  }

}
