import { Component, Inject, OnInit } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from 'app/modelos/details';

@Component({
  selector: 'app-select-impresora',
  templateUrl: './select-impresora.component.html',
  styleUrls: ['./select-impresora.component.css']
})
export class SelectImpresoraComponent implements OnInit {

  constructor(public dialog: MatDialog,
    @Inject(MAT_DIALOG_DATA) public data: Details
    ) { }

  ngOnInit(): void {
  }

  cancelar() {
    this.dialog.closeAll();

  }


}
