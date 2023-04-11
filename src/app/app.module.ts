import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { AppRoutingModule } from './app.routing';
import { ComponentsModule } from './components/components.module';
import { AppComponent } from './app.component';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { LoginModule } from './login/login.module';
import { SharedModule } from './components/shared/shared.module';
import { ProductosComponent } from './inventarios/productos/productos.component';
import { SeguridadComponent } from './seguridad/seguridad/seguridad.component';
import { UsuarioComponent } from './seguridad/usuario/usuario.component';
import { OpenDialogComponent } from './dialog/open-dialog/open-dialog.component';
import { SucursalesComponent } from './seguridad/sucursales/sucursales.component';
import { ProveedoresComponent } from './proveedores/proveedores.component';

@NgModule({
  imports: [
    BrowserAnimationsModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    RouterModule,
    AppRoutingModule,
    LoginModule,
    SharedModule,
    ComponentsModule

  ],
  declarations: [
    AppComponent,
    AdminLayoutComponent,
    ProductosComponent,
    SeguridadComponent,
    UsuarioComponent,
    OpenDialogComponent,
    SucursalesComponent,
    ProveedoresComponent,



  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
