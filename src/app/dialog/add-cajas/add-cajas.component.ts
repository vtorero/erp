
import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Cajas } from '../../modelos/cajas';

@Component({
  selector: 'app-add-cajas',
  templateUrl: './add-cajas.component.html',
  styleUrls: ['./add-cajas.component.css']
})


export class AddCajasComponent implements OnInit {

dataSucursales:any;

constructor(
    private toastr: MatSnackBar,
    public dialogRef: MatDialogRef<AddCajasComponent>,
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

