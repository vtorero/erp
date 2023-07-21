import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModDescuentoComponent } from './mod-descuento.component';

describe('ModDescuentoComponent', () => {
  let component: ModDescuentoComponent;
  let fixture: ComponentFixture<ModDescuentoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ModDescuentoComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ModDescuentoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
