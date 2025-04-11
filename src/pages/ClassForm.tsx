
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Class, Teacher } from "@/types/models";
import { getClass, addClass, updateClass, getTeachers } from "@/services/database";
import { toast } from "@/components/ui/use-toast";

const ClassForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditMode = !!id;

  const [formData, setFormData] = useState<Omit<Class, 'id'>>({
    name: "",
    capacity: 25,
    teacherId: "",
  });

  const [teachers, setTeachers] = useState<Teacher[]>([]);

  useEffect(() => {
    setTeachers(getTeachers());
    
    if (isEditMode && id) {
      const classData = getClass(id);
      if (classData) {
        setFormData({
          name: classData.name,
          capacity: classData.capacity,
          teacherId: classData.teacherId,
        });
      }
    }
  }, [id, isEditMode]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    const processedValue = name === "capacity" ? parseInt(value) : value;
    setFormData({ ...formData, [name]: processedValue });
  };

  const handleSelectChange = (name: string, value: string) => {
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (isEditMode && id) {
        updateClass({ id, ...formData });
        toast({
          title: "Success",
          description: "Class updated successfully",
        });
      } else {
        addClass(formData);
        toast({
          title: "Success",
          description: "New class added successfully",
        });
      }
      navigate("/classes");
    } catch (error) {
      toast({
        title: "Error",
        description: "There was an error saving the class",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">{isEditMode ? "Edit Class" : "Add New Class"}</h1>
      </div>

      <form onSubmit={handleSubmit}>
        <div className="max-w-lg mx-auto">
          <Card>
            <CardHeader>
              <CardTitle>Class Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Class Name</Label>
                <Input
                  id="name"
                  name="name"
                  required
                  value={formData.name}
                  onChange={handleInputChange}
                  placeholder="e.g., Year 3 Alpha"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="capacity">Class Capacity</Label>
                <Input
                  id="capacity"
                  name="capacity"
                  type="number"
                  required
                  min={1}
                  max={50}
                  value={formData.capacity}
                  onChange={handleInputChange}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="teacherId">Assigned Teacher</Label>
                <Select
                  value={formData.teacherId}
                  onValueChange={(value) => handleSelectChange("teacherId", value)}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Select a teacher" />
                  </SelectTrigger>
                  <SelectContent>
                    {teachers.map((teacher) => (
                      <SelectItem key={teacher.id} value={teacher.id}>
                        {teacher.firstName} {teacher.lastName}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <div className="mt-2 text-sm">
                  <Button
                    type="button"
                    variant="link"
                    className="p-0 h-auto text-school-green"
                    onClick={() => navigate("/teachers/new")}
                  >
                    + Create new teacher
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="flex justify-end mt-6 space-x-4">
            <Button type="button" variant="outline" onClick={() => navigate("/classes")}>
              Cancel
            </Button>
            <Button type="submit" className="bg-school-yellow hover:bg-yellow-600 text-white">
              {isEditMode ? "Update Class" : "Add Class"}
            </Button>
          </div>
        </div>
      </form>
    </div>
  );
};

export default ClassForm;
