import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AccesosComponent } from './accesos/accesos.component';
import { SharedModule } from 'app/components/shared/shared.module';






@NgModule({
  declarations: [
    AccesosComponent
  ],
  imports: [
    CommonModule,
    SharedModule

  ]
})
export class LoginModule { }
