
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Parent } from "@/types/models";
import { getParent, addParent, updateParent } from "@/services/database";
import { toast } from "@/components/ui/use-toast";

const ParentForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditMode = !!id;

  const [formData, setFormData] = useState<Omit<Parent, 'id'>>({
    firstName: "",
    lastName: "",
    address: "",
    phoneNumber: "",
    email: "",
    relationship: "mother",
  });

  useEffect(() => {
    if (isEditMode && id) {
      const parentData = getParent(id);
      if (parentData) {
        setFormData({
          firstName: parentData.firstName,
          lastName: parentData.lastName,
          address: parentData.address,
          phoneNumber: parentData.phoneNumber,
          email: parentData.email,
          relationship: parentData.relationship,
        });
      }
    }
  }, [id, isEditMode]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSelectChange = (name: string, value: string) => {
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (isEditMode && id) {
        updateParent({ id, ...formData });
        toast({
          title: "Success",
          description: "Parent/guardian updated successfully",
        });
      } else {
        addParent(formData);
        toast({
          title: "Success",
          description: "New parent/guardian added successfully",
        });
      }
      navigate("/parents");
    } catch (error) {
      toast({
        title: "Error",
        description: "There was an error saving the parent/guardian",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">{isEditMode ? "Edit Parent/Guardian" : "Add New Parent/Guardian"}</h1>
      </div>

      <form onSubmit={handleSubmit}>
        <div className="max-w-lg mx-auto">
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
                <Label htmlFor="relationship">Relationship to Pupil</Label>
                <Select
                  value={formData.relationship}
                  onValueChange={(value) => handleSelectChange("relationship", value)}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Select relationship" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="mother">Mother</SelectItem>
                    <SelectItem value="father">Father</SelectItem>
                    <SelectItem value="guardian">Guardian</SelectItem>
                    <SelectItem value="other">Other</SelectItem>
                  </SelectContent>
                </Select>
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

          <div className="flex justify-end mt-6 space-x-4">
            <Button type="button" variant="outline" onClick={() => navigate("/parents")}>
              Cancel
            </Button>
            <Button type="submit" className="bg-indigo-500 hover:bg-indigo-600">
              {isEditMode ? "Update Parent/Guardian" : "Add Parent/Guardian"}
            </Button>
          </div>
        </div>
      </form>
    </div>
  );
};

export default ParentForm;
