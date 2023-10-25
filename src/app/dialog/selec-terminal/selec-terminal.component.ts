import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Details } from 'app/modelos/details';

@Component({
  selector: 'app-selec-terminal',
  templateUrl: './selec-terminal.component.html',
  styleUrls: ['./selec-terminal.component.css']
})
export class SelecTerminalComponent implements OnInit {
  sucursales:any;
  constructor(public api:ApiService,
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details) { }

  ngOnInit(): void {
    this.cargaSucursales();
  }

  cargaSucursales() {
    let id = localStorage.getItem("currentId");
    this.api.getPermisos(id).subscribe(x => {
      this.sucursales = x;
      },
      error => {
        console.log('Error de conexion de datatable!' + error);
      });
  }

}
