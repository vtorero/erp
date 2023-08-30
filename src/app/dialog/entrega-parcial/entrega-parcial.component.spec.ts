import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EntregaParcialComponent } from './entrega-parcial.component';

describe('EntregaParcialComponent', () => {
  let component: EntregaParcialComponent;
  let fixture: ComponentFixture<EntregaParcialComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EntregaParcialComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EntregaParcialComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
