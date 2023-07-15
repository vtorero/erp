import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModPrecioComponent } from './mod-precio.component';

describe('ModPrecioComponent', () => {
  let component: ModPrecioComponent;
  let fixture: ComponentFixture<ModPrecioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ModPrecioComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ModPrecioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
