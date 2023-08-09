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
                {path:'venta-rapida',component:VentasComponent}]},
      {path:'inventarios',component:MainInventarioComponent},
    { path: 'proveedores',      component: ProveedoresComponent },
    { path: 'clientes',      component: ClienteComponent },
    { path: 'seguridad',      component: UsuarioComponent },
    { path: 'usuarios',      component: UsuarioComponent },
    { path: 'sucursales',      component: SucursalesComponent },
    { path: 'productos',      component: ProductosComponent },
    { path: 'user-profile',   component: UserProfileComponent },
    { path: 'table-list',     component: TableListComponent },
    { path: 'typography',     component: TypographyComponent },
    { path: 'icons',          component: IconsComponent },
    { path: 'maps',           component: MapsComponent },
    { path: 'notifications',  component: NotificationsComponent },
    { path: 'upgrade',        component: UpgradeComponent }
];
