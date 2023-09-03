import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VerVentaComponent } from './ver-venta.component';

describe('VerVentaComponent', () => {
  let component: VerVentaComponent;
  let fixture: ComponentFixture<VerVentaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ VerVentaComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(VerVentaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
