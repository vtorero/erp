import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EntregaFinalComponent } from './entrega-final.component';

describe('EntregaFinalComponent', () => {
  let component: EntregaFinalComponent;
  let fixture: ComponentFixture<EntregaFinalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EntregaFinalComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EntregaFinalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
