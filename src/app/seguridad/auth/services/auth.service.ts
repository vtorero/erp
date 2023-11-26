import { Injectable } from "@angular/core";
import { HttpClient} from "@angular/common/http";
import { Observable, of } from "rxjs";

@Injectable({
    providedIn: "root",
  })
  export class AuthService{

    constructor(private http:HttpClient){}


    checkAuthentication(): Observable<boolean> {

        if (!(localStorage.getItem('currentId')=="1")) return of(false);



      }

  }
