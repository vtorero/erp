import { Component,  ElementRef,  OnInit, ViewChild,NgZone} from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';
import { ApiService } from 'app/api.service';



function activarEnvio(event){
  console.log(event)

  }
  declare const grecaptcha: any;
@Component({
  selector: 'app-accesos',
  templateUrl: './accesos.component.html',
  styleUrls: ['./accesos.component.css']
})






export class AccesosComponent implements OnInit {

  @ViewChild('recaptchaContainer')
  recaptchaContainer!: ElementRef;

  captchaValid = false;
  widgetId: number | null = null;

  ngAfterViewInit(): void {
    this.loadRecaptcha();
  }


form:FormGroup;
loading = false;

  constructor(
    private router:Router,
    private ngZone: NgZone,
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

const usuario = this.form.value.usuario;
const password = this.form.value.password;
if(usuario!="" && password!=""){
this.loginUser(usuario,password)
}else{
  this._snackBar.open('Debe ingresar los datos de acceso','OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
  return;
}

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
    if(usuario!=null && password!=null){
        this.api.loginUser(usuario,password).subscribe(data=>{
          if(data['rows']>=1) {
            console.log(data);
            localStorage.removeItem("currentId");
            sessionStorage.removeItem("currentUser");
            localStorage.removeItem("currentNombre");
            localStorage.removeItem("currentAvatar");
            localStorage.removeItem("currentEmpresa");
            sessionStorage.removeItem("hashsession");
            sessionStorage.removeItem("sucursal_id");
            localStorage.removeItem("sucursal_id");
            localStorage.setItem("currentId",data['data'][0]['id']);
            localStorage.setItem("rol",data['data'][0]['rol']);
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
            if(data['data'][0]['rol']=='administrador'){
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


renderCaptcha(): void {

  if (this.widgetId !== null) return;

  this.widgetId = grecaptcha.render(
    this.recaptchaContainer.nativeElement,
    {
      sitekey: '6LeXLUonAAAAAE01suSHv1GYg7C8aIf2qD5Jfl-v',

      callback: (response: string) => {
        this.ngZone.run(() => {
          this.captchaValid = !!response;
        });
      },

      'expired-callback': () => {
        this.ngZone.run(() => {
          this.captchaValid = false;
        });
      },

      'error-callback': () => {
        this.ngZone.run(() => {
          this.captchaValid = false;
        });
      }
    }
  );
}


loadRecaptcha(): void {

  if (window['grecaptcha' as keyof Window]) {
    this.renderCaptcha();
    return;
  }

  if (document.getElementById('recaptcha-script')) {
    return;
  }

  const script = document.createElement('script');

  script.id = 'recaptcha-script';
  script.src =
    'https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit';

  script.async = true;
  script.defer = true;

  (window as any).onRecaptchaLoad = () => {
    this.renderCaptcha();
  };

  document.body.appendChild(script);
}

ngOnDestroy(): void {
  this.captchaValid = false;
}


ngOnInit(): void {
}

}








