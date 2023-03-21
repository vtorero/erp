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
    { path: '/user-profile', title: 'User Profile',  icon:'person', class: '',child:[{pat:'/icons', tit: 'Iconos',  icn:'bubble_chart',cls:'person'}]},
    { path: '/table-list', title: 'Table List',  icon:'content_paste', class: '' ,child:[]},
    { path: '/typography', title: 'Typography',  icon:'library_books', class: '' ,child:[]},
    { path: '/productos', title: 'Productos',  icon:'library_books', class: '' ,child:[]},
    { path: '/icons', title: 'Icons',  icon:'bubble_chart', class: '' ,child:[]},
    { path: '/maps', title: 'Maps',  icon:'location_on', class: '' ,child:[]},
    { path: '/seguridad', title: 'Seguridad',  icon:'notifications', class: '' ,child:[{pat:'/usuarios', tit: 'Usuarios',  icn:'person',cls:'person'},
    {pat:'/sucursales', tit: 'Sucursales',  icn:'person',cls:'person'}]}
];

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  menuItems: any[];

  constructor(private _serviceRutas:ServicesService) { }


  cargarRuta(ruta:string){
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
