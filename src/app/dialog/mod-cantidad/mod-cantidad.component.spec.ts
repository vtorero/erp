import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModCantidadComponent } from './mod-cantidad.component';

describe('ModCantidadComponent', () => {
  let component: ModCantidadComponent;
  let fixture: ComponentFixture<ModCantidadComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ModCantidadComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ModCantidadComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
