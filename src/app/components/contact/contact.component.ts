import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, FormControlName, Validators } from '@angular/forms';
import { StoreMailService } from 'src/app/services/store-mail.service';
@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.css']
})
export class ContactComponent implements OnInit {

  form: FormGroup;
  message: string;
  error:   string;
  code:    number;

  storeMailSuccess = false;
  storeMailFail    = false;
  loading          = false;

  constructor( public _storeMail: StoreMailService ) { }

  ngOnInit() {
    this.form = new FormGroup({
      email: new FormControl('', [Validators.email, Validators.required]),
      service: new FormControl('', [Validators.required]),
      message: new FormControl()
    });
  }

  send() {
    this.loading = true;
    if ( this.form.valid ) {
      this._storeMail.storeMail( this.form.value )
      .subscribe(
          ( res: any ) => {
            this.storeMailSuccess = true;
            this.storeMailFail    = false;
            this.loading          = false;
        },
          ( err: any ) => {
            this.storeMailSuccess = false;
            this.storeMailFail    = true;
            this.loading          = false;
            this.code             = err.status;
            this.error            = err.statusText;
            this.message          = err.error.message;
        }
      );
    }
  }

  closeAlert( e: any, mode: string ) {
    e.preventDefault(); e.stopPropagation();
    if( mode === 'success' ) {
      this.storeMailSuccess = false;
    } else {
      this.storeMailFail = false;
    }
  }
}
