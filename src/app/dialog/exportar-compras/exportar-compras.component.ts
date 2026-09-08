import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Exportar } from 'app/modelos/exportar';

@Component({
  selector: 'app-exportar-compras',
  templateUrl: './exportar-compras.component.html',
  styleUrls: ['./exportar-compras.component.css']
})
export class ExportarComprasComponent implements OnInit {
  titulo:string='';

  constructor(
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Exportar,
    @Inject(MAT_DIALOG_DATA) public datos: any

  ) { }

  ngOnInit(): void {
    this.data.fechafin =new Date();
    this.data.fechainicio=new Date();
    this.datos.titulo;
    console.log(this.datos.titulo);

  }

}
