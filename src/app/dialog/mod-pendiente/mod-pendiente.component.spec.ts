import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModPendienteComponent } from './mod-pendiente.component';

describe('ModPendienteComponent', () => {
  let component: ModPendienteComponent;
  let fixture: ComponentFixture<ModPendienteComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ModPendienteComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ModPendienteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
