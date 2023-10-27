import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FinanzasComponent } from './finanzas.component';

describe('FinanzasComponent', () => {
  let component: FinanzasComponent;
  let fixture: ComponentFixture<FinanzasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ FinanzasComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FinanzasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
