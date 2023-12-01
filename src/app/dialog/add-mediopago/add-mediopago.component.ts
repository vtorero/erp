import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Cajas } from '../../modelos/cajas';

@Component({
  selector: 'app-add-mediopago',
  templateUrl: './add-mediopago.component.html',
  styleUrls: ['./add-mediopago.component.css']
})

export class AddMediopagoComponent implements OnInit {

dataSucursales:any;

constructor(
    private toastr: MatSnackBar,
    public dialogRef: MatDialogRef<AddMediopagoComponent>,
    @Inject(MAT_DIALOG_DATA) public data:Cajas,
    private api:ApiService

  ) { }

  getSucursal(): void {
    this.api.getApiTabla('/sucursales').subscribe(data => {
      if(data) {
        this.dataSucursales = data;
      }
    } );
  }




  ngOnInit() {
    this.getSucursal();
  }
  cancelar() {
    this.dialogRef.close();
  }
}

