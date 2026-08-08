import { Component, Inject, OnInit } from "@angular/core";
import { MAT_DIALOG_DATA, MatDialog } from "@angular/material/dialog";
import { ApiService } from "app/api.service";
import { Productos } from "../../modelos/producto";
import { Details } from "app/modelos/details";

@Component({
  selector: "app-busca-producto",
  templateUrl: "./busca-producto.component.html",
  styleUrls: ["./busca-producto.component.css"],
})
export class BuscaProductoComponent implements OnInit {
  dataCategoria: any;
  criterio: string = "";
  categoria: string = "";
  subcategoria: string = "";
  familia: string = "";
  dataSource: any;
  sucursal: string = "";
  sucursal_id: string = "";
  dataProductos: any;
  dataSubCategoria: any;
  dataFamilia: any;

  constructor(
    public dialog: MatDialog,
    private api: ApiService,
    @Inject(MAT_DIALOG_DATA) public dataprod: Details
  ) {}

  ngOnInit(): void {
    this.getCate();
  }

  getCate(): void {
    this.api.getApi("categorias").subscribe((data) => {
      if (data) {
        this.dataCategoria = data;
      }
    });
  }

  public seleccionarCategoria(event: any) {
    const value = event.value;
    console.log(value);
    this.categoria = value;
    this.api.BuscarPorCategoria(value).subscribe((x) => {
      this.dataSubCategoria = x;
    });
    this.api
      .BuscarPorFamilia(value, this.subcategoria, this.familia, "categoria")
      .subscribe((x) => {
        this.dataSource = x;
      });
  }

  public seleccionarSubcategoria(event: any) {
    const value = event.value;
    this.subcategoria = value;
    console.log("subcategoria", this.familia);
    console.log(value);
    this.api.BuscarPorSubcategoria(value).subscribe((x) => {
      this.dataFamilia = x;
    });
    this.api
      .BuscarPorFamilia(this.categoria, value, this.familia, "subcategoria")
      .subscribe((x) => {
        this.dataSource = x;
      });
  }

  public seleccionarFamilia(event: any) {
    const value = event.value;
    console.log(value);
    this.api
      .BuscarPorFamilia(this.categoria, this.subcategoria, value, "familia")
      .subscribe((x) => {
        this.dataProductos = x;
      });
  }

  applyFilter(filterValue: string) {
    this.criterio = filterValue;
    filterValue = filterValue.trim();
    filterValue = filterValue.toLowerCase();
    if (filterValue != "") {
      this.api.BuscarProducto(filterValue).subscribe((x) => {
        this.dataSource = x;
      });
    }
  }

  seleccionarProducto(event) {
    console.log(event.value);
    this.api.getApiTablaCriterio('productos',event.value).subscribe(x=>{
      console.log(x[0].precio)
      this.dataprod.precio = x[0].precio
    })

  }

  onKey(value) {
    if (value != "") {
      this.selectSearch(value);
    }
  }

  selectSearch(value: string) {
    this.api.apiBuscadorProducto(value).subscribe((data) => {
      if (data) {
        console.log(data);
        this.dataProductos = data;
      }
    });
  }



  cancelar() {
    this.dialog.closeAll();
  }
}
