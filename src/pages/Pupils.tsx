
import { Button } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { useEffect, useState } from "react";
import { Pupil, Class } from "@/types/models";
import { getPupils, getClasses, deletePupil } from "@/services/database";
import { Link, useNavigate } from "react-router-dom";
import { Search, PlusCircle, Edit, Trash } from "lucide-react";
import { Input } from "@/components/ui/input";

const Pupils = () => {
  const [pupils, setPupils] = useState<Pupil[]>([]);
  const [classes, setClasses] = useState<Class[]>([]);
  const [searchTerm, setSearchTerm] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    setPupils(getPupils());
    setClasses(getClasses());
  }, []);

  const getClassName = (classId: string) => {
    const classObject = classes.find((c) => c.id === classId);
    return classObject ? classObject.name : "Unknown";
  };

  const handleDelete = (id: string) => {
    if (confirm("Are you sure you want to delete this pupil?")) {
      deletePupil(id);
      setPupils(getPupils());
    }
  };

  const filteredPupils = pupils.filter(
    (pupil) =>
      pupil.firstName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      pupil.lastName.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Pupils</h1>
        <Link to="/pupils/new">
          <Button className="bg-school-blue hover:bg-blue-600">
            <PlusCircle className="mr-2 h-4 w-4" /> Add New Pupil
          </Button>
        </Link>
      </div>

      <div className="flex items-center">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
          <Input
            placeholder="Search pupils..."
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
              <TableHead>Date of Birth</TableHead>
              <TableHead>Class</TableHead>
              <TableHead>Address</TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredPupils.map((pupil) => (
              <TableRow key={pupil.id}>
                <TableCell className="font-medium">
                  {pupil.firstName} {pupil.lastName}
                </TableCell>
                <TableCell>{new Date(pupil.dateOfBirth).toLocaleDateString()}</TableCell>
                <TableCell>{getClassName(pupil.classId)}</TableCell>
                <TableCell>{pupil.address}</TableCell>
                <TableCell>
                  <div className="flex space-x-2">
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => navigate(`/pupils/${pupil.id}/edit`)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      className="text-red-500"
                      onClick={() => handleDelete(pupil.id)}
                    >
                      <Trash className="h-4 w-4" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
            {filteredPupils.length === 0 && (
              <TableRow>
                <TableCell colSpan={5} className="text-center py-10">
                  No pupils found. Add a new pupil to get started.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
};

export default Pupils;
