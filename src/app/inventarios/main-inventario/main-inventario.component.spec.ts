import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MainInventarioComponent } from './main-inventario.component';

describe('MainInventarioComponent', () => {
  let component: MainInventarioComponent;
  let fixture: ComponentFixture<MainInventarioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MainInventarioComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MainInventarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
