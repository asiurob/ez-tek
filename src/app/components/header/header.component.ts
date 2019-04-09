import { Component, OnInit } from '@angular/core';
import { ItemHeader } from '../../interfaces/items-header.interface';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  itemsErp: ItemHeader[] = [
    {
      icon: 'fa-donate',
      title: 'Contabilidad',
      url: '/erp/contabilidad',
      description: 'Timbra, factura y consolida tus finanzas'
    },
    {
      icon: 'fa-chart-line',
      title: 'Ventas',
      url: '/erp/ventas',
      description: 'Incrementa tus ventas y monitorea en tiempo real'
    },
    {
      icon: 'fa-box',
      title: 'Almacén',
      url: '/erp/almacen',
      description: 'Ubicaciones, movimientos y productos en un solo lugar'
    }
  ];

  itemsDevelop: ItemHeader[] = [
    {
      icon: 'fa-mobile-alt',
      title: 'Aplicación móvil',
      url: '/develop/mobile',
      description: 'Mejora la experiencia de tus clientes con una app'
    },
    {
      icon: 'fa-globe',
      title: 'Aplicación web',
      url: '/develop/web',
      description: 'Si no tienes un sitio web, estás perdiendo el 50% de tu mercado, ¡No te quedes fuera!'
    }
  ];
  
  constructor() { }

  ngOnInit() {
  }

}
