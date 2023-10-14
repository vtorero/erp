import { Routes } from '@angular/router';

import { DashboardComponent } from '../../dashboard/dashboard.component';
import { UserProfileComponent } from '../../user-profile/user-profile.component';
import { TableListComponent } from '../../table-list/table-list.component';
import { TypographyComponent } from '../../typography/typography.component';
import { IconsComponent } from '../../icons/icons.component';
import { MapsComponent } from '../../maps/maps.component';
import { NotificationsComponent } from '../../notifications/notifications.component';
import { UpgradeComponent } from '../../upgrade/upgrade.component';
import { AccesosComponent } from '../../login/accesos/accesos.component';
import { ProductosComponent } from '../../inventarios/productos/productos.component';
import { SeguridadComponent } from '../../seguridad/seguridad/seguridad.component';
import { UsuarioComponent } from '../../seguridad/usuario/usuario.component';
import { SucursalesComponent } from '../../seguridad/sucursales/sucursales.component';
import { ProveedoresComponent } from '../../proveedores/proveedores.component';
import { ClienteComponent } from '../../negocio/cliente/cliente.component';
import { VentasComponent } from 'app/negocio/ventas/ventas.component';
import { MainInventarioComponent } from '../../inventarios/main-inventario/main-inventario.component';
import { ListadoComponent } from '../../negocio/ventas/listado/listado.component';
import { MainComponent } from 'app/negocio/ventas/main/main.component';
import { ConfiguracionComponent } from '../../seguridad/configuracion/configuracion.component';
import { permisosComponent } from '../../seguridad/permisos/permisos.component';
import { CajasComponent } from 'app/seguridad/cajas/cajas.component';
import { KardexComponent } from 'app/inventarios/kardex/kardex.component';
import { ComprasComponent } from 'app/negocio/compras/compras.component';
import { ComprasRegistroComponent } from 'app/negocio/compras/compras-registro/compras-registro.component';
import { ListadoComprasComponent } from 'app/negocio/compras/listado-compras/listado-compras.component';
import { VendedoresComponent } from '../../negocio/vendedores/vendedores.component';

export const AdminLayoutRoutes: Routes = [
    // {
    //   path: '',
    //   children: [ {
    //     path: 'dashboard',
    //     component: DashboardComponent
    // }]}, {
    // path: '',
    // children: [ {
    //   path: 'userprofile',
    //   component: UserProfileComponent
    // }]
    // }, {
    //   path: '',
    //   children: [ {
    //     path: 'icons',
    //     component: IconsComponent
    //     }]
    // }, {
    //     path: '',
    //     children: [ {
    //         path: 'notifications',
    //         component: NotificationsComponent
    //     }]
    // }, {
    //     path: '',
    //     children: [ {
    //         path: 'maps',
    //         component: MapsComponent
    //     }]
    // }, {
    //     path: '',
    //     children: [ {
    //         path: 'typography',
    //         component: TypographyComponent
    //     }]
    // }, {
    //     path: '',
    //     children: [ {
    //         path: 'upgrade',
    //         component: UpgradeComponent
    //     }]
    // }
    { path: 'dashboard', component: DashboardComponent },
    {path:'ventas',
    children:[{path:'main',component:MainComponent},
    {path:'listado',component:ListadoComponent},
    {path:'venta-rapida',component:VentasComponent},
    { path: 'clientes', component: ClienteComponent }]},
    {path:'compras',
    children:[{path:'main',component:ComprasComponent},
    {path:'listado',component:ListadoComprasComponent},
    {path:'registro-compras',component:ComprasRegistroComponent},
    { path: 'proveedores', component: ProveedoresComponent }]},
    {path:'configuracion',
    children:[{path:'main',component:ConfiguracionComponent}
    ,{path:'permisos',component:permisosComponent},
    {path:'cajas',component:CajasComponent}]},
      {path:'inventarios',component:MainInventarioComponent},
      {path:'kardex',component:KardexComponent},
    { path: 'seguridad',      component: UsuarioComponent },
    { path: 'usuarios',      component: UsuarioComponent },
    { path: 'sucursales',      component: SucursalesComponent },
    { path: 'vendedores',      component: VendedoresComponent },
    { path: 'productos',      component: ProductosComponent },
    { path: 'user-profile',   component: UserProfileComponent },
    { path: 'table-list',     component: TableListComponent },
    { path: 'typography',     component: TypographyComponent },
    { path: 'icons',          component: IconsComponent },
    { path: 'maps',           component: MapsComponent },
    { path: 'notifications',  component: NotificationsComponent },
    { path: 'upgrade',        component: UpgradeComponent }
];
