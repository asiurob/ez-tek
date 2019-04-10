import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BACKEND_URL } from '../global/variables';
import { StoreMail } from '../interfaces/store-mail.interface';
import { Observable } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class StoreMailService {

  constructor( private _http: HttpClient ) { }

  storeMail( data: StoreMail ): Observable<object> {
    return this._http.post( `${BACKEND_URL}/contact/mail.php`, { data } );
  }
}
