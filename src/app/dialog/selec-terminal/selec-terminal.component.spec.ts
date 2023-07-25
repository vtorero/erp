import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SelecTerminalComponent } from './selec-terminal.component';

describe('SelecTerminalComponent', () => {
  let component: SelecTerminalComponent;
  let fixture: ComponentFixture<SelecTerminalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SelecTerminalComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SelecTerminalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
