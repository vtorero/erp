import { Component, OnInit } from '@angular/core';
import { ServicesService } from '../../services.service';

declare const $: any;
declare interface RouteInfo {
    path: string;
    title: string;
    icon: string;
    class: string;
    child:Child[];
}

declare interface Child {
  pat: string;
  tit:string;
   icn:string;
    cls:string;
}
export const ROUTES: RouteInfo[] = [
    { path: '/dashboard', title: 'Dashboard',  icon: 'dashboard', class: '',child:[]},
    { path: '/ventas/main', title: 'Ventas',  icon: 'library_books', class: '',child:[{pat:'/ventas/venta-rapida', tit: 'Venta Rápida',  icn:'bubble_chart',cls:'notifications'},{pat:'/ventas/listado', tit: 'Listado',  icn:'bubble_chart',cls:'notifications'}]},
    { path: '/clientes', title: 'Clientes',  icon: 'person', class: '',child:[]},
    { path: '/proveedores', title: 'Proveedores',  icon: 'dashboard', class: '',child:[]},
    { path: '/inventarios', title: 'Inventarios',  icon:'library_books', class: '' ,child:[{pat:'/productos', tit: 'Productos',  icn:'bubble_chart',cls:'notifications'}]},
    //{ path: '/user-profile', title: 'User Profile',  icon:'person', class: '',child:[{pat:'/icons', tit: 'Iconos',  icn:'bubble_chart',cls:'person'}]},
    //{ path: '/table-list', title: 'Table List',  icon:'content_paste', class: '' ,child:[]},
    //{ path: '/typography', title: 'Typography',  icon:'library_books', class: '' ,child:[]},
    //{ path: '/icons', title: 'Icons',  icon:'bubble_chart', class: '' ,child:[]},
    //{ path: '/maps', title: 'Maps',  icon:'location_on', class: '' ,child:[]},
    { path: '/seguridad', title: 'Seguridad',  icon:'notifications', class: '' ,child:[{pat:'/usuarios', tit: 'Usuarios',  icn:'person',cls:'person'},
       {pat:'/sucursales', tit: 'Sucursales',  icn:'person',cls:'person'}]},
    { path: '/configuracion', title: 'Configuración',  icon: 'dashboard', class: '',child:[]},
];

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  menuItems: any[];

  constructor(private _serviceRutas:ServicesService) { }


  cargarRuta(ruta:never){
    console.log("ruta Cargada");
  this._serviceRutas.add(ruta)

  }

  ngOnInit() {
    this.menuItems = ROUTES.filter(menuItem => menuItem);
  }
  isMobileMenu() {
      if ($(window).width() > 991) {
          return false;
      }
      return true;
  };
}
