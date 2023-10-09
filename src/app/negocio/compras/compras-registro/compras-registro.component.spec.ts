import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ComprasRegistroComponent } from './compras-registro.component';

describe('ComprasRegistroComponent', () => {
  let component: ComprasRegistroComponent;
  let fixture: ComponentFixture<ComprasRegistroComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ComprasRegistroComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ComprasRegistroComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
