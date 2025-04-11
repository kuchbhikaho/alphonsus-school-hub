
import { Button } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Card, CardContent } from "@/components/ui/card";
import { useEffect, useState } from "react";
import { Class, Teacher, Pupil } from "@/types/models";
import { getClasses, getTeachers, deleteClass, getPupilsByClass } from "@/services/database";
import { Link, useNavigate } from "react-router-dom";
import { Search, PlusCircle, Edit, Trash, Users } from "lucide-react";
import { Input } from "@/components/ui/input";

const Classes = () => {
  const [classes, setClasses] = useState<Class[]>([]);
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [searchTerm, setSearchTerm] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    setClasses(getClasses());
    setTeachers(getTeachers());
  }, []);

  const getTeacherName = (teacherId: string) => {
    const teacher = teachers.find((t) => t.id === teacherId);
    return teacher ? `${teacher.firstName} ${teacher.lastName}` : "Unassigned";
  };

  const getPupilCount = (classId: string) => {
    return getPupilsByClass(classId).length;
  };

  const handleDelete = (id: string) => {
    if (confirm("Are you sure you want to delete this class?")) {
      deleteClass(id);
      setClasses(getClasses());
    }
  };

  const filteredClasses = classes.filter(
    (cls) => cls.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Classes</h1>
        <Link to="/classes/new">
          <Button className="bg-school-yellow hover:bg-yellow-600 text-white">
            <PlusCircle className="mr-2 h-4 w-4" /> Add New Class
          </Button>
        </Link>
      </div>

      <div className="flex items-center">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
          <Input
            placeholder="Search classes..."
            className="pl-10"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredClasses.map((cls) => (
          <Card key={cls.id} className="overflow-hidden">
            <div className="bg-school-yellow h-2"></div>
            <CardContent className="pt-6">
              <div className="flex justify-between items-start mb-4">
                <div>
                  <h2 className="text-xl font-semibold">{cls.name}</h2>
                  <p className="text-sm text-gray-500">
                    Capacity: {getPupilCount(cls.id)}/{cls.capacity} pupils
                  </p>
                </div>
                <div className="flex space-x-2">
                  <Button
                    size="sm"
                    variant="outline"
                    onClick={() => navigate(`/classes/${cls.id}/edit`)}
                  >
                    <Edit className="h-4 w-4" />
                  </Button>
                  <Button
                    size="sm"
                    variant="outline"
                    className="text-red-500"
                    onClick={() => handleDelete(cls.id)}
                  >
                    <Trash className="h-4 w-4" />
                  </Button>
                </div>
              </div>

              <div className="space-y-2 mt-4">
                <div className="flex items-center space-x-2 text-sm">
                  <Users className="h-4 w-4 text-gray-500" />
                  <span>Teacher: {getTeacherName(cls.teacherId)}</span>
                </div>
              </div>

              <div className="mt-6">
                <Button
                  variant="outline"
                  size="sm"
                  className="w-full"
                  onClick={() => navigate(`/classes/${cls.id}/pupils`)}
                >
                  View Pupils
                </Button>
              </div>
            </CardContent>
          </Card>
        ))}

        {filteredClasses.length === 0 && (
          <div className="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 bg-white rounded-lg shadow">
            <p className="text-gray-500">No classes found. Add a new class to get started.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default Classes;
