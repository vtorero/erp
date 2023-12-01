import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from 'app/modelos/details';

@Component({
  selector: 'app-mod-despacho',
  templateUrl: './mod-despacho.component.html',
  styleUrls: ['./mod-despacho.component.css']
})
export class ModDespachoComponent implements OnInit {

  constructor(
    public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details) {

     }

  ngOnInit(): void {
  }
  cancelar() {

    this.dialog.closeAll();

  }
}