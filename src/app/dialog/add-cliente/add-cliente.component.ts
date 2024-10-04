import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Proveedor } from 'app/modelos/proveedor';

@Component({
  selector: 'app-add-cliente',
  templateUrl: './add-cliente.component.html',
  styleUrls: ['./add-cliente.component.css']
})



export class AddClienteComponent implements OnInit {
  public selectedMomentReport1 = new Date();
  public selectedMomentReport2 = new Date();
  constructor(
    public dialogRef: MatDialogRef<AddClienteComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Proveedor,
    @Inject(MAT_DIALOG_DATA) public datos: {clase:string,fechaInicio:string,fechaFin:string},

  ) { }

    /*onLoadDatos(event:any){
    if(event.target.value!=""){
    this.api.getProveedor(event.target.value).subscribe(data => {
      if(data) {
        console.log(data);
        this.data.razon_social=data['razonSocial']
        this.data.direccion=data['direccion']
        this.data.departamento=data['departamento']
        this.data.provincia=data['provincia']
        this.data.distrito=data['distrito']
      }
    },
    error=>{
      console.log(error)
      this.toastr.open("Numero de RUC incorrecto","OK");
    } );
  }else{
    this.toastr.open("Debe indicar el Numero de RUC","OK");
  }
 }*/

  ngOnInit() {
  }
  cancelar() {
    this.dialogRef.close();
  }
}
