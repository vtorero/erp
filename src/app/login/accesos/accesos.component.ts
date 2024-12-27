import { Component,  OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';
import { ApiService } from 'app/api.service';



function activarEnvio(event){
  console.log(event)

  }

@Component({
  selector: 'app-accesos',
  templateUrl: './accesos.component.html',
  styleUrls: ['./accesos.component.css']
})






export class AccesosComponent implements OnInit {
form:FormGroup;
loading = false;

  constructor(
    private router:Router,
    private api:ApiService,
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
this.loginUser(usuario,password)
}

/*fakeLoading(){
this.loading=true;
setTimeout(() => {
    this._router.navigate(['dashboard'])
  //this.loading=false;
}, 1500)

}
*/

loginUser(usuario,password){
  this.loading=true;
  event.preventDefault();
    if(usuario){
        this.api.loginUser(usuario,password).subscribe(data=>{
          if(data['rows']==1) {
            console.log(data['data'][0]);
            localStorage.removeItem("currentId");
            sessionStorage.removeItem("currentUser");
            localStorage.removeItem("currentNombre");
            localStorage.removeItem("currentAvatar");
            localStorage.removeItem("currentEmpresa");
            sessionStorage.removeItem("hashsession");
            sessionStorage.removeItem("sucursal_id");
            localStorage.removeItem("sucursal_id");
            localStorage.setItem("currentId",data['data'][0]['id']);
            localStorage.setItem("id_suc",data['data'][0]['id_sucursal']);
            localStorage.setItem("sucursal",data['data'][0]['sucursal']);
            localStorage.setItem("direccion",data['data'][0]['direccion']);
            localStorage.setItem("telefono",data['data'][0]['telefono']);
            localStorage.setItem("email",data['data'][0]['email']);
            localStorage.setItem("currentNombre",data['data'][0]['nombre']);
            localStorage.setItem("currentEmpresa",data['data'][0]['nombre']);
            localStorage.setItem("impresora",data['data'][0]['avatar']);
            sessionStorage.setItem("currentUser",data['data'][0]['nombre']);
            sessionStorage.setItem("hashsession",data['data'][0]['hash']);
            sessionStorage.setItem("sucursal_id",data['data'][0]['id_sucursal']);
            localStorage.setItem("sucursal_id",data['data'][0]['id_sucursal']);
            if(data['data'][0]['nombre']=='admin'){
            this.router.navigate(['/dashboard']);
          }else{
            this.router.navigate(['/ventas/venta-rapida']);
          }

          }else{

            this.error();


          }

        });
  }
//this.router.navigate(['dash']);
}

error(){
  this.router.navigate(['/login']);
this._snackBar.open('Usuario o contraseña son inválidos','OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
this.loading=false;
}

  ngOnInit(): void {
  }

}
