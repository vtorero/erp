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

constructor(
    private toastr: MatSnackBar,
    public dialogRef: MatDialogRef<AddPermisosComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Permisos,
    private api:ApiService

  ) { }

  getUsers(): void {
    this.api.getApi('usuarios').subscribe(data => {
      if(data) {
        this.dataUsuarios = data;
      }
    } );
  }


  ngOnInit() {
    this.getUsers();
  }
  cancelar() {
    this.dialogRef.close();
  }
}
