import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Permisos } from 'app/modelos/permisos';

@Component({
  selector: 'app-add-permisos',
  templateUrl: './add-permisos.component.html',
  styleUrls: ['./add-permisos.component.css']
})



export class AddPermisosComponent implements OnInit {
dataUsuarios:any;
dataSucursales:any;

constructor(
    private toastr: MatSnackBar,
    public dialogRef: MatDialogRef<AddPermisosComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Permisos,
    private api:ApiService

  ) { }

  getSucursal(): void {
    this.api.getApiTabla('/sucursales').subscribe(data => {
      if(data) {
        this.dataSucursales = data;
      }
    } );
  }


  getUsers(): void {
    this.api.getApi('usuarios').subscribe(data => {
      if(data) {
        this.dataUsuarios = data;
      }
    } );
  }


  ngOnInit() {
    this.getUsers();
    this.getSucursal();
  }
  cancelar() {
    this.dialogRef.close();
  }
}
