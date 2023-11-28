import { Component, OnInit } from '@angular/core';
import { ApiService } from 'app/api.service';
import { Router } from '@angular/router';
import { BarratareasComponent } from '../../../components/barratareas/barratareas.component';
@Component({
  selector: 'app-main',
  templateUrl: './main.component.html',
  styleUrls: ['./main.component.css']
})
export class MainComponent implements OnInit {

  constructor(
    private api: ApiService,
    private router:Router

  ) { }

  ngOnInit(): void {
    if(this.api.getCurrentUser==false){
      this.router.navigate(['/']);
      }
  }

}
