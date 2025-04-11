
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
import { Parent } from "@/types/models";
import { getParents, deleteParent } from "@/services/database";
import { Link, useNavigate } from "react-router-dom";
import { Search, PlusCircle, Edit, Trash } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";

const Parents = () => {
  const [parents, setParents] = useState<Parent[]>([]);
  const [searchTerm, setSearchTerm] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    setParents(getParents());
  }, []);

  const handleDelete = (id: string) => {
    if (confirm("Are you sure you want to delete this parent/guardian?")) {
      deleteParent(id);
      setParents(getParents());
    }
  };

  const filteredParents = parents.filter(
    (parent) =>
      parent.firstName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      parent.lastName.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const getRelationshipBadge = (relationship: string) => {
    switch (relationship) {
      case 'mother':
        return <Badge className="bg-purple-100 text-purple-800">Mother</Badge>;
      case 'father':
        return <Badge className="bg-blue-100 text-blue-800">Father</Badge>;
      case 'guardian':
        return <Badge className="bg-green-100 text-green-800">Guardian</Badge>;
      default:
        return <Badge className="bg-gray-100 text-gray-800">{relationship}</Badge>;
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Parents & Guardians</h1>
        <Link to="/parents/new">
          <Button className="bg-indigo-500 hover:bg-indigo-600">
            <PlusCircle className="mr-2 h-4 w-4" /> Add New Parent/Guardian
          </Button>
        </Link>
      </div>

      <div className="flex items-center">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
          <Input
            placeholder="Search parents..."
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
              <TableHead>Relationship</TableHead>
              <TableHead>Contact</TableHead>
              <TableHead>Address</TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredParents.map((parent) => (
              <TableRow key={parent.id}>
                <TableCell className="font-medium">
                  {parent.firstName} {parent.lastName}
                </TableCell>
                <TableCell>{getRelationshipBadge(parent.relationship)}</TableCell>
                <TableCell>
                  <div>{parent.phoneNumber}</div>
                  <div className="text-sm text-gray-500">{parent.email}</div>
                </TableCell>
                <TableCell className="max-w-xs truncate">{parent.address}</TableCell>
                <TableCell>
                  <div className="flex space-x-2">
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => navigate(`/parents/${parent.id}/edit`)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      className="text-red-500"
                      onClick={() => handleDelete(parent.id)}
                    >
                      <Trash className="h-4 w-4" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
            {filteredParents.length === 0 && (
              <TableRow>
                <TableCell colSpan={5} className="text-center py-10">
                  No parents/guardians found. Add a new parent/guardian to get started.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
};

export default Parents;
