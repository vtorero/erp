import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Exportar } from 'app/modelos/exportar';

@Component({
  selector: 'app-exportar-compras',
  templateUrl: './exportar-compras.component.html',
  styleUrls: ['./exportar-compras.component.css']
})
export class ExportarComprasComponent implements OnInit {

  constructor(
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Exportar

  ) { }

  ngOnInit(): void {
    this.data.fechafin =new Date();
    this.data.fechainicio=new Date();
  }

}
