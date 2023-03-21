import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BarratareasComponent } from './barratareas.component';

describe('BarratareasComponent', () => {
  let component: BarratareasComponent;
  let fixture: ComponentFixture<BarratareasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ BarratareasComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BarratareasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
