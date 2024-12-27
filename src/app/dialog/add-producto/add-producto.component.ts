import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA, MatDialog } from '@angular/material/dialog';
import { ApiService } from 'app/api.service';
import { Productos } from '../../modelos/producto';
import { AddCategoriaComponent } from '../add-categoria/add-categoria.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AddSubCategoriaComponent } from '../add-sub-categoria/add-sub-categoria.component';
import { AddFamiliaComponent } from '../add-familia/add-familia.component';


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
id_categoria:any;
AddImagen:boolean=false;
archivo = {
  nombre: null,
  nombreArchivo: null,
  base64textString: null
}


  constructor(
    public dialog: MatDialog,
    private _snackBar: MatSnackBar,
    public dialogRef: MatDialogRef<AddProductoComponent>,
    @Inject(MAT_DIALOG_DATA) public data: Productos,
    private api:ApiService
  ) {

  }

  ngOnInit() {
    this.api.getSelectApi('articulo/',this.data.id).subscribe(x => {
      this.response=x;
      console.log("this response",this.response[0].id_categoria)
      this.id_categoria=this.response[0].id_categoria;
    });

    this.getCate();
     this.getSubCategoria();
    this.getFamilia();
    this.getunidad();
  }

  public seleccionarCategoria(event) {
    const value = event.value;
    this.api.BuscarPorSubCategoriaCategoria(value).subscribe(x => {
      this.dataSubCategoria=x;

    });

 }

 public seleccionarSubcategoria(event) {
   const value = event.value;
   this.api.BuscarFamilaPorSubcategoria(value).subscribe(x => {
   this.dataFamilia=x;
  });

}

public seleccionarFamilia(event) {
  const value = event.value;
  console.log(value)
  this.api.BuscarPorFamilia(this.dataCategoria,this.dataSubCategoria,value,'familia').subscribe(x => {
  this.dataSource=x;

 });
}


  getCate(): void {
    this.api.getApi('categorias').subscribe(data => {
      if(data) {
        console.log("cate",data)
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
    this.AddImagen=true;
    var files = event.target.files;
    var file = files[0];
    this.data.nombre_imagen=file.name;
     if(files && file) {
             var reader = new FileReader();
       reader.onload = this._handleReaderLoaded.bind(this);
       reader.readAsBinaryString(file);
     }
  }

  _handleReaderLoaded(readerEvent) {
    var binaryString = readerEvent.target.result;
     this.data.imagen=btoa(binaryString);

  }

  cancelar() {
    this.dialogRef.close();
  }

  openCategoria(enterAnimationDuration: string, exitAnimationDuration: string,id:number){
    let index=0;
    const dialogo2=this.dialog.open(AddCategoriaComponent, {width: 'auto',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modPendiente',id:id},
    });
     dialogo2.afterClosed().subscribe(ux => {
           this.api.guardarCategoria(ux).subscribe(
          data=>{

            this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            this.getCate();
            },
          erro=>{console.log(erro)}
            );



     });

  }

  openSubCategoria(enterAnimationDuration: string, exitAnimationDuration: string,id:number){
    let index=0;
    const dialogo2=this.dialog.open(AddSubCategoriaComponent, {width: '450',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modPendiente',id:id},
    });
     dialogo2.afterClosed().subscribe(ux => {
           this.api.guardarSubCategoria(ux).subscribe(
          data=>{

            this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            this.getSubCategoria();
            },
          erro=>{console.log(erro)}
            );



     });

  }

  openFamilia(enterAnimationDuration: string, exitAnimationDuration: string,id:number){
    let index=0;
    const dialogo2=this.dialog.open(AddFamiliaComponent, {width: '450',enterAnimationDuration,exitAnimationDuration,
    data: {clase:'modPendiente',id:id},
    });
     dialogo2.afterClosed().subscribe(ux => {
           this.api.guardarFamilia(ux).subscribe(
          data=>{

            this._snackBar.open(data['messaje'],'OK',{duration:5000,horizontalPosition:'center',verticalPosition:'top'});
            this.getFamilia();
            },
          erro=>{console.log(erro)}
            );



     });

  }

}