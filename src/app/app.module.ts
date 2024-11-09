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
import { NgxMaskModule } from 'ngx-mask';
import { ListadoComponent } from './negocio/ventas/listado/listado.component';
import { ConfiguracionComponent } from './seguridad/configuracion/configuracion.component';
import { permisosComponent } from './seguridad/permisos/permisos.component';
import { AddPermisosComponent } from './dialog/add-permisos/add-permisos.component';
import { CajasComponent } from './seguridad/cajas/cajas.component';
import { AddCajasComponent } from './dialog/add-cajas/add-cajas.component';
import { EntregaParcialComponent } from './dialog/entrega-parcial/entrega-parcial.component';
import { ModPendienteComponent } from './dialog/mod-pendiente/mod-pendiente.component';
import { VerVentaComponent } from './negocio/ventas/ver-venta/ver-venta.component';
import { EntregaFinalComponent } from './dialog/entrega-final/entrega-final.component';
import { AgregarInventarioComponent } from './dialog/agregar-inventario/agregar-inventario.component';
import { KardexComponent } from './inventarios/kardex/kardex.component';
import { ComprasComponent } from './negocio/compras/compras.component';
import { MainComponent } from './negocio/ventas/main/main.component';
import { ComprasRegistroComponent } from './negocio/compras/compras-registro/compras-registro.component';
import { ListadoComprasComponent } from './negocio/compras/listado-compras/listado-compras.component';
import { PagoPendienteComponent } from './dialog/pago-pendiente/pago-pendiente.component';
import { VendedoresComponent } from './negocio/vendedores/vendedores.component';
import { RegistroCompraComponent } from './dialog/registro-compra/registro-compra.component';
import { VerCompraComponent } from './negocio/compras/ver-compra/ver-compra.component';
import { FinanzasComponent } from './negocio/finanzas/finanzas.component';
import { SelectImpresoraComponent } from './dialog/select-impresora/select-impresora.component';
import { ModoPagoComponent } from './seguridad/modo-pago/modo-pago.component';
import { ModDespachoComponent } from './dialog/mod-despacho/mod-despacho.component';
import { AddMediopagoComponent } from './dialog/add-mediopago/add-mediopago.component';
import { ExportarComprasComponent } from './dialog/exportar-compras/exportar-compras.component';
import { AddCategoriaComponent } from './dialog/add-categoria/add-categoria.component';
import { AddSubCategoriaComponent } from './dialog/add-sub-categoria/add-sub-categoria.component';



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
    ComponentsModule,
    NgxMaskModule.forRoot()
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
     ListadoComponent,
     ConfiguracionComponent,
     permisosComponent,
     AddPermisosComponent,
     CajasComponent,
     AddCajasComponent,
     EntregaParcialComponent,
     ModPendienteComponent,
     VerVentaComponent,
     EntregaFinalComponent,
     AgregarInventarioComponent,
     KardexComponent,
     ComprasComponent,
     ComprasRegistroComponent,
     ListadoComprasComponent,
     PagoPendienteComponent,
     VendedoresComponent,
     MainComponent,
     RegistroCompraComponent,
     VerCompraComponent,
     FinanzasComponent,
     SelectImpresoraComponent,
     ModoPagoComponent,
     ModDespachoComponent,
     AddMediopagoComponent,
     ExportarComprasComponent,
     AddCategoriaComponent,
     AddSubCategoriaComponent,

  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
