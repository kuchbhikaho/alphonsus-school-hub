
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Users, UserCheck, School, GraduationCap } from "lucide-react";
import { useEffect, useState } from "react";
import { getTeachers, getClasses, getPupils, getParents } from "@/services/database";
import { Link } from "react-router-dom";

const Dashboard = () => {
  const [teacherCount, setTeacherCount] = useState(0);
  const [classCount, setClassCount] = useState(0);
  const [pupilCount, setPupilCount] = useState(0);
  const [parentCount, setParentCount] = useState(0);

  useEffect(() => {
    setTeacherCount(getTeachers().length);
    setClassCount(getClasses().length);
    setPupilCount(getPupils().length);
    setParentCount(getParents().length);
  }, []);

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Dashboard</h1>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <Card className="bg-white">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Pupils</CardTitle>
            <Users className="h-4 w-4 text-school-blue" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{pupilCount}</div>
            <Link to="/pupils" className="text-xs text-school-blue hover:underline">
              View all pupils
            </Link>
          </CardContent>
        </Card>
        
        <Card className="bg-white">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Teachers</CardTitle>
            <UserCheck className="h-4 w-4 text-school-green" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{teacherCount}</div>
            <Link to="/teachers" className="text-xs text-school-green hover:underline">
              View all teachers
            </Link>
          </CardContent>
        </Card>
        
        <Card className="bg-white">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Classes</CardTitle>
            <School className="h-4 w-4 text-school-yellow" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{classCount}</div>
            <Link to="/classes" className="text-xs text-yellow-600 hover:underline">
              View all classes
            </Link>
          </CardContent>
        </Card>
        
        <Card className="bg-white">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Parents</CardTitle>
            <GraduationCap className="h-4 w-4 text-indigo-500" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{parentCount}</div>
            <Link to="/parents" className="text-xs text-indigo-500 hover:underline">
              View all parents
            </Link>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card className="bg-white">
          <CardHeader>
            <CardTitle>Welcome to St Alphonsus School Management System</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-gray-500">
              This dashboard provides an overview of all school data. Use the navigation to manage pupils, teachers, classes, and parents.
            </p>
            <p className="mt-4 text-gray-500">
              Quick actions:
            </p>
            <div className="mt-2 space-x-2">
              <Link to="/pupils/new" className="inline-block mt-2 px-4 py-2 bg-school-blue text-white rounded hover:bg-blue-600">Add New Pupil</Link>
              <Link to="/teachers/new" className="inline-block mt-2 px-4 py-2 bg-school-green text-white rounded hover:bg-green-600">Add New Teacher</Link>
            </div>
          </CardContent>
        </Card>
        
        <Card className="bg-white">
          <CardHeader>
            <CardTitle>Recent Updates</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-start space-x-4">
                <span className="bg-blue-100 p-2 rounded-full">
                  <Users className="h-4 w-4 text-school-blue" />
                </span>
                <div>
                  <p className="text-sm font-medium">New School Year Registration Open</p>
                  <p className="text-xs text-gray-500">Registration for the new school year is now open. Please ensure all pupil information is up to date.</p>
                </div>
              </div>
              
              <div className="flex items-start space-x-4">
                <span className="bg-green-100 p-2 rounded-full">
                  <School className="h-4 w-4 text-school-green" />
                </span>
                <div>
                  <p className="text-sm font-medium">Class Assignments Updated</p>
                  <p className="text-xs text-gray-500">Class assignments for the upcoming term have been updated. Please check the class listings.</p>
                </div>
              </div>
              
              <div className="flex items-start space-x-4">
                <span className="bg-yellow-100 p-2 rounded-full">
                  <UserCheck className="h-4 w-4 text-school-yellow" />
                </span>
                <div>
                  <p className="text-sm font-medium">Teacher Training Day</p>
                  <p className="text-xs text-gray-500">Reminder: Teacher training day scheduled for next Friday. School will be closed for pupils.</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default Dashboard;
