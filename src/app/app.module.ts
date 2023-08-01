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
import { AddProveedorComponent } from './dialog/add-proveedor/add-proveedor.component';
import { ClienteComponent } from './negocio/cliente/cliente.component';
import { VentasComponent } from './negocio/ventas/ventas.component';
import { AddClienteComponent } from './dialog/add-cliente/add-cliente.component';
import { MainInventarioComponent } from './inventarios/main-inventario/main-inventario.component';
import { AddProductoComponent } from './dialog/add-producto/add-producto.component';
import { ModPrecioComponent } from './dialog/mod-precio/mod-precio.component';
import { ModCantidadComponent } from './dialog/mod-cantidad/mod-cantidad.component';
import { ModDescuentoComponent } from './dialog/mod-descuento/mod-descuento.component';
import { SelecTerminalComponent } from './dialog/selec-terminal/selec-terminal.component';
import { RegistroVentaComponent } from './dialog/registro-venta/registro-venta.component';

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
    AddProveedorComponent,
    ClienteComponent,
    VentasComponent,
    AddClienteComponent,
     MainInventarioComponent,
     AddProductoComponent,
     ModPrecioComponent,
     ModCantidadComponent,
     ModDescuentoComponent,
     SelecTerminalComponent,
     RegistroVentaComponent,



  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
