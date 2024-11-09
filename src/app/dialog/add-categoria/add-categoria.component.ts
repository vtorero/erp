import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Categoria } from 'app/modelos/categoria';
import { DetaPagos } from 'app/modelos/detapagos';

@Component({
  selector: 'app-add-categoria',
  templateUrl: './add-categoria.component.html',
  styleUrls: ['./add-categoria.component.css']
})
export class AddCategoriaComponent implements OnInit {

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: Categoria

  ) { }

  ngOnInit(): void {
  }

}
