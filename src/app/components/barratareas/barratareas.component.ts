import { Component} from '@angular/core';
import { Router } from '@angular/router';
import { ServicesService } from 'app/services.service';



@Component({
  selector: 'app-barratareas',
  templateUrl: './barratareas.component.html',
  styleUrls: ['./barratareas.component.css'],

})
export class BarratareasComponent{

constructor(private _router:Router,private _sercicioRutas:ServicesService){}


  tabs = [];

ngOnInit(): void {
  this.tabs=this._sercicioRutas.getRutas();
console.log("init",this.tabs)
}

  go(url:string){
    console.log(url)
    this._router.navigate([url]);
  }

  closeTab(index: number) {
    console.log("delindex",index+1);
    var total=this.tabs.length;
      setTimeout(() => {
      console.log("total",total);
if(index+1<total){
  console.log("es menor:"+this.tabs[index+1])
      this._router.navigate([this.tabs[index+1]])
      this.tabs=this._sercicioRutas.del(index);
     }else{
      console.log("es mayor")
      this.tabs=this._sercicioRutas.del(index);
      this._router.navigate([this.tabs[index-1]])
     }

     }, 1000);


  }

}
