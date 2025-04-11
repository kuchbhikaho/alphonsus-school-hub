
import { Button } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { useEffect, useState } from "react";
import { Teacher, Class } from "@/types/models";
import { getTeachers, getClasses, deleteTeacher } from "@/services/database";
import { Link, useNavigate } from "react-router-dom";
import { Search, PlusCircle, Edit, Trash } from "lucide-react";
import { Input } from "@/components/ui/input";

const Teachers = () => {
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [classes, setClasses] = useState<Class[]>([]);
  const [searchTerm, setSearchTerm] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    setTeachers(getTeachers());
    setClasses(getClasses());
  }, []);

  const getClassName = (classId?: string) => {
    if (!classId) return "Not assigned";
    const classObject = classes.find((c) => c.id === classId);
    return classObject ? classObject.name : "Unknown";
  };

  const getBadgeColor = (status: string) => {
    switch (status) {
      case 'passed':
        return "bg-green-100 text-green-800";
      case 'pending':
        return "bg-yellow-100 text-yellow-800";
      case 'failed':
        return "bg-red-100 text-red-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  const handleDelete = (id: string) => {
    if (confirm("Are you sure you want to delete this teacher?")) {
      deleteTeacher(id);
      setTeachers(getTeachers());
    }
  };

  const filteredTeachers = teachers.filter(
    (teacher) =>
      teacher.firstName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      teacher.lastName.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Teachers</h1>
        <Link to="/teachers/new">
          <Button className="bg-school-green hover:bg-green-600">
            <PlusCircle className="mr-2 h-4 w-4" /> Add New Teacher
          </Button>
        </Link>
      </div>

      <div className="flex items-center">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
          <Input
            placeholder="Search teachers..."
            className="pl-10"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      </div>

      <div className="bg-white rounded-lg shadow">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Class</TableHead>
              <TableHead>Contact</TableHead>
              <TableHead>Background Check</TableHead>
              <TableHead>Salary</TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredTeachers.map((teacher) => (
              <TableRow key={teacher.id}>
                <TableCell className="font-medium">
                  {teacher.firstName} {teacher.lastName}
                </TableCell>
                <TableCell>{getClassName(teacher.classId)}</TableCell>
                <TableCell>
                  <div>{teacher.phoneNumber}</div>
                  <div className="text-sm text-gray-500">{teacher.email}</div>
                </TableCell>
                <TableCell>
                  <Badge 
                    className={getBadgeColor(teacher.backgroundCheckStatus)}
                  >
                    {teacher.backgroundCheckStatus.charAt(0).toUpperCase() + teacher.backgroundCheckStatus.slice(1)}
                  </Badge>
                </TableCell>
                <TableCell>£{teacher.annualSalary.toLocaleString()}</TableCell>
                <TableCell>
                  <div className="flex space-x-2">
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => navigate(`/teachers/${teacher.id}/edit`)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      className="text-red-500"
                      onClick={() => handleDelete(teacher.id)}
                    >
                      <Trash className="h-4 w-4" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
            {filteredTeachers.length === 0 && (
              <TableRow>
                <TableCell colSpan={6} className="text-center py-10">
                  No teachers found. Add a new teacher to get started.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
};

export default Teachers;
