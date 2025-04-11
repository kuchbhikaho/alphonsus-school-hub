
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Teacher } from "@/types/models";
import { getTeacher, addTeacher, updateTeacher } from "@/services/database";
import { toast } from "@/components/ui/use-toast";

const TeacherForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditMode = !!id;

  const [formData, setFormData] = useState<Omit<Teacher, 'id'>>({
    firstName: "",
    lastName: "",
    address: "",
    phoneNumber: "",
    email: "",
    annualSalary: 0,
    backgroundCheckStatus: 'pending',
  });

  useEffect(() => {
    if (isEditMode && id) {
      const teacherData = getTeacher(id);
      if (teacherData) {
        setFormData({
          firstName: teacherData.firstName,
          lastName: teacherData.lastName,
          address: teacherData.address,
          phoneNumber: teacherData.phoneNumber,
          email: teacherData.email,
          annualSalary: teacherData.annualSalary,
          backgroundCheckStatus: teacherData.backgroundCheckStatus,
        });
      }
    }
  }, [id, isEditMode]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    const processedValue = name === "annualSalary" ? parseInt(value) : value;
    setFormData({ ...formData, [name]: processedValue });
  };

  const handleSelectChange = (name: string, value: string) => {
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (isEditMode && id) {
        updateTeacher({ id, ...formData });
        toast({
          title: "Success",
          description: "Teacher updated successfully",
        });
      } else {
        addTeacher(formData);
        toast({
          title: "Success",
          description: "New teacher added successfully",
        });
      }
      navigate("/teachers");
    } catch (error) {
      toast({
        title: "Error",
        description: "There was an error saving the teacher",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">{isEditMode ? "Edit Teacher" : "Add New Teacher"}</h1>
      </div>

      <form onSubmit={handleSubmit}>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Personal Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="firstName">First Name</Label>
                  <Input
                    id="firstName"
                    name="firstName"
                    required
                    value={formData.firstName}
                    onChange={handleInputChange}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="lastName">Last Name</Label>
                  <Input
                    id="lastName"
                    name="lastName"
                    required
                    value={formData.lastName}
                    onChange={handleInputChange}
                  />
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="address">Address</Label>
                <Input
                  id="address"
                  name="address"
                  required
                  value={formData.address}
                  onChange={handleInputChange}
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Contact Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="phoneNumber">Phone Number</Label>
                <Input
                  id="phoneNumber"
                  name="phoneNumber"
                  required
                  value={formData.phoneNumber}
                  onChange={handleInputChange}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  name="email"
                  type="email"
                  required
                  value={formData.email}
                  onChange={handleInputChange}
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Employment Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="annualSalary">Annual Salary (£)</Label>
                <Input
                  id="annualSalary"
                  name="annualSalary"
                  type="number"
                  required
                  value={formData.annualSalary}
                  onChange={handleInputChange}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="backgroundCheckStatus">Background Check Status</Label>
                <Select
                  value={formData.backgroundCheckStatus}
                  onValueChange={(value) => handleSelectChange("backgroundCheckStatus", value)}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Select status" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="passed">Passed</SelectItem>
                    <SelectItem value="pending">Pending</SelectItem>
                    <SelectItem value="failed">Failed</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="flex justify-end mt-6 space-x-4">
          <Button type="button" variant="outline" onClick={() => navigate("/teachers")}>
            Cancel
          </Button>
          <Button type="submit" className="bg-school-green hover:bg-green-600">
            {isEditMode ? "Update Teacher" : "Add Teacher"}
          </Button>
        </div>
      </form>
    </div>
  );
};

export default TeacherForm;
