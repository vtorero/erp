import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from 'app/modelos/details';

@Component({
  selector: 'app-mod-pendiente',
  templateUrl: './mod-pendiente.component.html',
  styleUrls: ['./mod-pendiente.component.css']
})
export class ModPendienteComponent implements OnInit {

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

