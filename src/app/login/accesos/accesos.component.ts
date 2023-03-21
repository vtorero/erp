import { Component,  OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';


@Component({
  selector: 'app-accesos',
  templateUrl: './accesos.component.html',
  styleUrls: ['./accesos.component.css']
})



export class AccesosComponent implements OnInit {
form:FormGroup;
loading = false;

  constructor(
    private fb:FormBuilder,
    private _snackBar: MatSnackBar,
    private _router:Router) {
    this.form=this.fb.group({
      usuario:['',Validators.required],
      password:['',Validators.required],
    });

  }

  ingresar(){
console.log(this.form)
const usuario = this.form.value.usuario;
const password = this.form.value.password;

if(usuario=='admin' && password=='123'){
this.fakeLoading()

}else{
this.error();
this.form.reset()

}
}

fakeLoading(){
this.loading=true;
setTimeout(() => {
    this._router.navigate(['dashboard'])
  //this.loading=false;
}, 1500);


}

error(){
this._snackBar.open('Usuario o contraseña son inválidos','OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});

}

  ngOnInit(): void {
  }

}
