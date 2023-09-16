import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from 'app/modelos/details';

@Component({
  selector: 'app-entrega-final',
  templateUrl: './entrega-final.component.html',
  styleUrls: ['./entrega-final.component.css']
})
export class EntregaFinalComponent implements OnInit {

  constructor(
    @Inject(MAT_DIALOG_DATA) public detalle:Details,
  ) { }

  ngOnInit(): void {
  }

}
