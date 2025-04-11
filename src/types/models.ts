
// Data models for our application

export interface Teacher {
  id: string;
  firstName: string;
  lastName: string;
  address: string;
  phoneNumber: string;
  email: string;
  annualSalary: number;
  backgroundCheckStatus: 'passed' | 'pending' | 'failed';
  classId?: string;
}

export interface Class {
  id: string;
  name: string;
  capacity: number;
  teacherId: string;
}

export interface Parent {
  id: string;
  firstName: string;
  lastName: string;
  address: string;
  phoneNumber: string;
  email: string;
  relationship: 'mother' | 'father' | 'guardian' | 'other';
}

export interface Pupil {
  id: string;
  firstName: string;
  lastName: string;
  dateOfBirth: string;
  address: string;
  medicalInformation: string;
  classId: string;
  parentIds: string[];
}
