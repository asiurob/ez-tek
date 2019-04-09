import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, FormControlName, Validators } from '@angular/forms';
@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.css']
})
export class ContactComponent implements OnInit {

  form: FormGroup;
  constructor() { }

  ngOnInit() {
    this.form = new FormGroup({
      email: new FormControl('', [Validators.email, Validators.required]),
      service: new FormControl('', [Validators.required]),
      message: new FormControl()
    });
  }

  send() {
    if ( this.form.valid ) {
      console.log( this.form.value );
    }
  }

}
