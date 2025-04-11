
import { Button } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { useState, useEffect } from "react";
import { useParams, useNavigate, Link } from "react-router-dom";
import { Pupil, Class, Teacher } from "@/types/models";
import { getPupilsByClass, getClass, getTeacher } from "@/services/database";

const ClassPupils = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [pupils, setPupils] = useState<Pupil[]>([]);
  const [classData, setClassData] = useState<Class | null>(null);
  const [teacher, setTeacher] = useState<Teacher | null>(null);

  useEffect(() => {
    if (!id) return;

    const cls = getClass(id);
    if (cls) {
      setClassData(cls);
      setPupils(getPupilsByClass(id));
      
      const teacherData = getTeacher(cls.teacherId);
      if (teacherData) {
        setTeacher(teacherData);
      }
    } else {
      navigate("/classes");
    }
  }, [id, navigate]);

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold">{classData?.name} - Pupils</h1>
          <p className="text-gray-500 mt-1">
            {pupils.length} of {classData?.capacity} pupils
          </p>
        </div>
        <div className="space-x-4">
          <Button
            variant="outline"
            onClick={() => navigate("/classes")}
          >
            Back to Classes
          </Button>
          <Link to="/pupils/new">
            <Button className="bg-school-blue hover:bg-blue-600">
              Add New Pupil
            </Button>
          </Link>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="md:col-span-2">
          <div className="bg-white rounded-lg shadow">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Date of Birth</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {pupils.map((pupil) => (
                  <TableRow key={pupil.id}>
                    <TableCell className="font-medium">
                      {pupil.firstName} {pupil.lastName}
                    </TableCell>
                    <TableCell>{new Date(pupil.dateOfBirth).toLocaleDateString()}</TableCell>
                    <TableCell>
                      <Button
                        size="sm"
                        variant="outline"
                        onClick={() => navigate(`/pupils/${pupil.id}/edit`)}
                      >
                        View Details
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
                {pupils.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={3} className="text-center py-10">
                      No pupils in this class. Add pupils to get started.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-white p-6 rounded-lg shadow">
            <h2 className="font-semibold text-lg mb-4">Class Information</h2>
            <div className="space-y-2">
              <div className="grid grid-cols-2">
                <span className="text-gray-500">Name:</span>
                <span>{classData?.name}</span>
              </div>
              <div className="grid grid-cols-2">
                <span className="text-gray-500">Capacity:</span>
                <span>{classData?.capacity}</span>
              </div>
              <div className="grid grid-cols-2">
                <span className="text-gray-500">Teacher:</span>
                <span>{teacher ? `${teacher.firstName} ${teacher.lastName}` : "Unassigned"}</span>
              </div>
            </div>
          </div>

          {teacher && (
            <div className="bg-white p-6 rounded-lg shadow">
              <h2 className="font-semibold text-lg mb-4">Teacher Contact</h2>
              <div className="space-y-2">
                <div className="grid grid-cols-2">
                  <span className="text-gray-500">Phone:</span>
                  <span>{teacher.phoneNumber}</span>
                </div>
                <div className="grid grid-cols-2">
                  <span className="text-gray-500">Email:</span>
                  <span className="truncate">{teacher.email}</span>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ClassPupils;
