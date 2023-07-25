import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from 'app/modelos/details';
import { ApiService } from '../../api.service';

@Component({
  selector: 'app-mod-descuento',
  templateUrl: './mod-descuento.component.html',
  styleUrls: ['./mod-descuento.component.css']
})
export class ModDescuentoComponent implements OnInit {
descuentoTotal:number=0;

  constructor(
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details,
    ) {

     }

  ngOnInit(): void {

  }
  cancelar() {

    this.dialog.closeAll();

  }

  cambiaDescuento(descuento:number,precio:number){
    console.log(descuento)
     this.descuentoTotal=(descuento/100);
  }




}
