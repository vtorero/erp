import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Details } from '../../modelos/details';

@Component({
  selector: 'app-mod-precio',
  templateUrl: './mod-precio.component.html',
  styleUrls: ['./mod-precio.component.css']
})
export class ModPrecioComponent implements OnInit {

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: Details,
  ) { }

  ngOnInit(): void {
  }

}
