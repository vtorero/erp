import { ComponentFixture, TestBed } from '@angular/core/testing';

import { permisosComponent } from './permisos.component';

describe('PermisosComponent', () => {
  let component: permisosComponent;
  let fixture: ComponentFixture<permisosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ permisosComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(permisosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
