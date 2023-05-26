import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Productos } from '../../modelos/producto';

@Component({
  selector: 'app-add-producto',
  templateUrl: './add-producto.component.html',
  styleUrls: ['./add-producto.component.css']
})
export class AddProductoComponent implements OnInit {
dataSource:any;
isLoaded:boolean=false
  constructor(
    private toastr: MatSnackBar,
    public dialogRef: MatDialogRef<AddProductoComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Productos,
    private api:ApiService


  ) { }

  ngOnInit(): void {
  }
  getCate(): void {
    this.api.getCategoriaSelect().subscribe(data => {
      if(data) {
        this.dataSource = data;
        this.isLoaded = true;
      }
    } );
  }

}
