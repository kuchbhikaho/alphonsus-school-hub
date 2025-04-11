
import { v4 as uuidv4 } from 'uuid';
import { Teacher, Class, Parent, Pupil } from '../types/models';

// Mock database storage
let teachers: Teacher[] = [];
let classes: Class[] = [];
let parents: Parent[] = [];
let pupils: Pupil[] = [];

// Sample data initialization
const initializeSampleData = () => {
  // Create sample teachers
  const teacher1: Teacher = {
    id: uuidv4(),
    firstName: 'John',
    lastName: 'Smith',
    address: '123 School Lane, Cityville',
    phoneNumber: '555-1234',
    email: 'john.smith@stalphonsus.edu',
    annualSalary: 45000,
    backgroundCheckStatus: 'passed'
  };
  
  const teacher2: Teacher = {
    id: uuidv4(),
    firstName: 'Mary',
    lastName: 'Johnson',
    address: '456 Education Street, Townsville',
    phoneNumber: '555-5678',
    email: 'mary.johnson@stalphonsus.edu',
    annualSalary: 48000,
    backgroundCheckStatus: 'passed'
  };
  
  teachers = [teacher1, teacher2];
  
  // Create sample classes
  const class1: Class = {
    id: uuidv4(),
    name: 'Year 1 Alpha',
    capacity: 25,
    teacherId: teacher1.id
  };
  
  const class2: Class = {
    id: uuidv4(),
    name: 'Year 2 Beta',
    capacity: 28,
    teacherId: teacher2.id
  };
  
  classes = [class1, class2];
  
  // Update teachers with class assignments
  teacher1.classId = class1.id;
  teacher2.classId = class2.id;
  
  // Create sample parents
  const parent1: Parent = {
    id: uuidv4(),
    firstName: 'Robert',
    lastName: 'Brown',
    address: '789 Family Road, Parentville',
    phoneNumber: '555-9012',
    email: 'robert.brown@email.com',
    relationship: 'father'
  };
  
  const parent2: Parent = {
    id: uuidv4(),
    firstName: 'Sarah',
    lastName: 'Brown',
    address: '789 Family Road, Parentville',
    phoneNumber: '555-3456',
    email: 'sarah.brown@email.com',
    relationship: 'mother'
  };
  
  parents = [parent1, parent2];
  
  // Create sample pupils
  const pupil1: Pupil = {
    id: uuidv4(),
    firstName: 'James',
    lastName: 'Brown',
    dateOfBirth: '2017-05-12',
    address: '789 Family Road, Parentville',
    medicalInformation: 'No allergies',
    classId: class1.id,
    parentIds: [parent1.id, parent2.id]
  };
  
  pupils = [pupil1];
};

// Initialize data immediately
initializeSampleData();

// Teacher CRUD operations
export const getTeachers = (): Teacher[] => {
  return [...teachers];
};

export const getTeacher = (id: string): Teacher | undefined => {
  return teachers.find(teacher => teacher.id === id);
};

export const addTeacher = (teacher: Omit<Teacher, 'id'>): Teacher => {
  const newTeacher = { ...teacher, id: uuidv4() };
  teachers.push(newTeacher);
  return newTeacher;
};

export const updateTeacher = (teacher: Teacher): Teacher => {
  const index = teachers.findIndex(t => t.id === teacher.id);
  if (index !== -1) {
    teachers[index] = teacher;
    return teacher;
  }
  throw new Error('Teacher not found');
};

export const deleteTeacher = (id: string): void => {
  teachers = teachers.filter(teacher => teacher.id !== id);
};

// Class CRUD operations
export const getClasses = (): Class[] => {
  return [...classes];
};

export const getClass = (id: string): Class | undefined => {
  return classes.find(cls => cls.id === id);
};

export const addClass = (cls: Omit<Class, 'id'>): Class => {
  const newClass = { ...cls, id: uuidv4() };
  classes.push(newClass);
  return newClass;
};

export const updateClass = (cls: Class): Class => {
  const index = classes.findIndex(c => c.id === cls.id);
  if (index !== -1) {
    classes[index] = cls;
    return cls;
  }
  throw new Error('Class not found');
};

export const deleteClass = (id: string): void => {
  classes = classes.filter(cls => cls.id !== id);
};

// Parent CRUD operations
export const getParents = (): Parent[] => {
  return [...parents];
};

export const getParent = (id: string): Parent | undefined => {
  return parents.find(parent => parent.id === id);
};

export const addParent = (parent: Omit<Parent, 'id'>): Parent => {
  const newParent = { ...parent, id: uuidv4() };
  parents.push(newParent);
  return newParent;
};

export const updateParent = (parent: Parent): Parent => {
  const index = parents.findIndex(p => p.id === parent.id);
  if (index !== -1) {
    parents[index] = parent;
    return parent;
  }
  throw new Error('Parent not found');
};

export const deleteParent = (id: string): void => {
  parents = parents.filter(parent => parent.id !== id);
};

// Pupil CRUD operations
export const getPupils = (): Pupil[] => {
  return [...pupils];
};

export const getPupil = (id: string): Pupil | undefined => {
  return pupils.find(pupil => pupil.id === id);
};

export const addPupil = (pupil: Omit<Pupil, 'id'>): Pupil => {
  const newPupil = { ...pupil, id: uuidv4() };
  pupils.push(newPupil);
  return newPupil;
};

export const updatePupil = (pupil: Pupil): Pupil => {
  const index = pupils.findIndex(p => p.id === pupil.id);
  if (index !== -1) {
    pupils[index] = pupil;
    return pupil;
  }
  throw new Error('Pupil not found');
};

export const deletePupil = (id: string): void => {
  pupils = pupils.filter(pupil => pupil.id !== id);
};

// Helper functions for related data
export const getPupilsByClass = (classId: string): Pupil[] => {
  return pupils.filter(pupil => pupil.classId === classId);
};

export const getParentsByPupil = (pupilId: string): Parent[] => {
  const pupil = pupils.find(p => p.id === pupilId);
  if (!pupil) return [];
  
  return parents.filter(parent => pupil.parentIds.includes(parent.id));
};

export const getTeacherByClass = (classId: string): Teacher | undefined => {
  const cls = classes.find(c => c.id === classId);
  if (!cls) return undefined;
  
  return teachers.find(teacher => teacher.id === cls.teacherId);
};
