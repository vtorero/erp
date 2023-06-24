import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from 'app/api.service';
import { Productos } from '../../modelos/producto';
import { filter } from 'rxjs';

@Component({
  selector: 'app-add-producto',
  templateUrl: './add-producto.component.html',
  styleUrls: ['./add-producto.component.css']
})
export class AddProductoComponent implements OnInit {
dataSource:any;
imageSrc: string = '';
dataCategoria:any;
dataUnidad:any;
dataSubCategoria:any;
dataFamilia:any;
isLoaded:boolean=false;
dataArray:any;
response:any;
id_cate:any;
archivo = {
  nombre: null,
  nombreArchivo: null,
  base64textString: null
}


  constructor(
    public dialogRef: MatDialogRef<AddProductoComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Productos,
    private api:ApiService
  ) {

  }

  ngOnInit() {
    this.api.getSelectApi('articulo/',this.data.id).subscribe(x => {

      this.response=x;
      console.log("this response",this.response[0].id_categoria)
      this.id_cate=this.response[0].id_categoria;
    });



    this.getCate();
    this.getSubCategoria();
    this.getFamilia();
    this.getunidad();
  }

  getCate(): void {
    this.api.getApi('categorias').subscribe(data => {
      if(data) {
        this.dataCategoria = data;
      }
    } );
  }

  getSubCategoria(): void {
    this.api.getApi('sub_categorias').subscribe(data => {
      if(data) {
        this.dataSubCategoria = data;
      }
    } );
  }

  getFamilia(): void {
    this.api.getApi('familia').subscribe(data => {
      if(data) {
        this.dataFamilia = data;
      }
    } );
  }

  getunidad(): void {
    this.api.getApi('unidad').subscribe(data => {
      if(data) {
        this.dataUnidad = data;

      }
    } );
  }
  onKeyCategoria(value:string) {
    this.dataArray = [];
    this.Search('categoria',value);
  }
  Search(tabla:string,value: string) {
    let criterio;
    if (value) {
      criterio = "/" + value
    } else {
      criterio ='';
    }
    console.log(value)
    this.api.getSelectApi(tabla, criterio).subscribe(data => {
      if (data) {
        this.dataCategoria = data;
      }
    });
  }
  seleccionarArchivo(event) {
    this.isLoaded=true;
    var files = event.target.files;
    var file = files[0];


     if(files && file) {
      this.data.nombre_imagen=file.name;
       var reader = new FileReader();
       reader.onload = this._handleReaderLoaded.bind(this);
       reader.readAsBinaryString(file);
     }



  }

  _handleReaderLoaded(readerEvent) {
    var binaryString = readerEvent.target.result;
    //this.archivo.base64textString =
    this.data.imagen=btoa(binaryString);

  }

  cancelar() {
    this.dialogRef.close();
  }



}
