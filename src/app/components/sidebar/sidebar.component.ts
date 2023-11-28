import { Component, OnInit } from '@angular/core';
import { ServicesService } from '../../services.service';

declare const $: any;
declare interface RouteInfo {
    path: string;
    title: string;
    icon: string;
    class: string;
    admin:string;
    child:Child[];
}

declare interface Child {
  pat: string;
  tit:string;
   icn:string;
    cls:string;
    admin:string;
}
export const ROUTES: RouteInfo[] = [
    { path: '/dashboard', title: 'Dashboard',  icon: 'dashboard', class:'', admin:'si',child:[]},
    { path: '/ventas/main', title: 'Ventas',  icon: 'library_books', class: '',admin:'no',
    child:[{pat:'/ventas/venta-rapida', tit: 'Venta Rápida',  icn:'bubble_chart',cls:'notifications', admin:'no'},
    {pat:'/ventas/clientes', tit: 'Clientes',  icn:'person',cls:'notifications', admin:'no'}]},
    { path: '/compras/main', title: 'Compras',  icon: 'library_books', class: '',admin:'si',
    child:[{pat:'/compras/registro-compras', tit: 'Registrar',  icn:'bubble_chart',cls:'notifications', admin:'si'},
    {pat:'/compras/listado', tit: 'Listado',  icn:'bubble_chart',cls:'notifications', admin:'si'},
    {pat:'/compras/proveedores', tit: 'Proveedores',  icn:'person',cls:'notifications', admin:'no'}]},
    { path: '/inventarios', title: 'Inventarios',  icon:'library_books', class: '',admin:'no' ,child:[{pat:'/productos', tit: 'Productos',  icn:'bubble_chart',cls:'notifications', admin:'no'},{pat:'/kardex', tit: 'Kardex',  icn:'bubble_chart',cls:'notifications', admin:'si'}]},
    { path: '/seguridad', title: 'Seguridad',  icon:'notifications', class: '' ,admin:'si',child:[
      {pat:'/usuarios', tit: 'Usuarios',  icn:'person',cls:'person', admin:'si'},
       {pat:'/sucursales', tit: 'Sucursales',  icn:'person',cls:'person', admin:'si'},
       {pat:'/vendedores', tit: 'Vendedores',  icn:'person',cls:'person', admin:'si'}]},
    { path: '/configuracion/permisos', title: 'Configuración',  icon: 'dashboard', class:'' ,admin:'si',child:[{pat:'/configuracion/permisos', tit: 'Permisos',  icn:'person',cls:'person', admin:'si'},
    {pat:'/configuracion/cajas', tit: 'Cajas',  icn:'dashboard',cls:'person', admin:'si'}]},
];

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  menuItems: any[];
  currentname:string='';

  constructor(private _serviceRutas:ServicesService) { }


  cargarRuta(ruta:never){
    console.log("ruta Cargada");
  this._serviceRutas.add(ruta)

  }

  ngOnInit() {
    this.currentname = localStorage.getItem("currentNombre");
    this.menuItems = ROUTES.filter(menuItem => menuItem);
  }
  isMobileMenu() {
      if ($(window).width() > 991) {
          return false;
      }
      return true;
  };
}
