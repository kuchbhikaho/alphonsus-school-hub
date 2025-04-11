
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Pupil, Class, Parent } from "@/types/models";
import { 
  getPupil, 
  addPupil, 
  updatePupil, 
  getClasses, 
  getParents,
  getParentsByPupil 
} from "@/services/database";
import { toast } from "@/components/ui/use-toast";

const PupilForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditMode = !!id;

  const [formData, setFormData] = useState<Omit<Pupil, 'id'>>({
    firstName: "",
    lastName: "",
    dateOfBirth: "",
    address: "",
    medicalInformation: "",
    classId: "",
    parentIds: [],
  });

  const [classes, setClasses] = useState<Class[]>([]);
  const [parents, setParents] = useState<Parent[]>([]);
  const [availableParents, setAvailableParents] = useState<Parent[]>([]);
  const [selectedParents, setSelectedParents] = useState<Parent[]>([]);
  
  useEffect(() => {
    // Load classes and parents
    setClasses(getClasses());
    setParents(getParents());
    setAvailableParents(getParents());
    
    // If in edit mode, load pupil data
    if (isEditMode && id) {
      const pupilData = getPupil(id);
      if (pupilData) {
        setFormData({
          firstName: pupilData.firstName,
          lastName: pupilData.lastName,
          dateOfBirth: pupilData.dateOfBirth,
          address: pupilData.address,
          medicalInformation: pupilData.medicalInformation,
          classId: pupilData.classId,
          parentIds: pupilData.parentIds,
        });
        
        const pupilParents = getParentsByPupil(id);
        setSelectedParents(pupilParents);
      }
    }
  }, [id, isEditMode]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSelectChange = (name: string, value: string) => {
    setFormData({ ...formData, [name]: value });
  };

  const handleAddParent = (parentId: string) => {
    const parent = parents.find(p => p.id === parentId);
    if (parent && !formData.parentIds.includes(parentId) && formData.parentIds.length < 2) {
      setFormData({
        ...formData,
        parentIds: [...formData.parentIds, parentId]
      });
      setSelectedParents([...selectedParents, parent]);
    }
  };

  const handleRemoveParent = (parentId: string) => {
    setFormData({
      ...formData,
      parentIds: formData.parentIds.filter(id => id !== parentId)
    });
    setSelectedParents(selectedParents.filter(parent => parent.id !== parentId));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (isEditMode && id) {
        updatePupil({ id, ...formData });
        toast({
          title: "Success",
          description: "Pupil updated successfully",
        });
      } else {
        addPupil(formData);
        toast({
          title: "Success",
          description: "New pupil added successfully",
        });
      }
      navigate("/pupils");
    } catch (error) {
      toast({
        title: "Error",
        description: "There was an error saving the pupil",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">{isEditMode ? "Edit Pupil" : "Add New Pupil"}</h1>
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
                <Label htmlFor="dateOfBirth">Date of Birth</Label>
                <Input
                  id="dateOfBirth"
                  name="dateOfBirth"
                  type="date"
                  required
                  value={formData.dateOfBirth}
                  onChange={handleInputChange}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="address">Address</Label>
                <Textarea
                  id="address"
                  name="address"
                  required
                  value={formData.address}
                  onChange={handleInputChange}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="medicalInformation">Medical Information</Label>
                <Textarea
                  id="medicalInformation"
                  name="medicalInformation"
                  value={formData.medicalInformation}
                  onChange={handleInputChange}
                  placeholder="Allergies, medical conditions, etc."
                />
              </div>
            </CardContent>
          </Card>

          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Class Information</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="classId">Class</Label>
                  <Select
                    value={formData.classId}
                    onValueChange={(value) => handleSelectChange("classId", value)}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Select a class" />
                    </SelectTrigger>
                    <SelectContent>
                      {classes.map((cls) => (
                        <SelectItem key={cls.id} value={cls.id}>
                          {cls.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Parent/Guardian Information</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label>Assigned Parents/Guardians ({selectedParents.length}/2)</Label>
                  <div className="mt-2 space-y-2">
                    {selectedParents.map((parent) => (
                      <div key={parent.id} className="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span>{parent.firstName} {parent.lastName} ({parent.relationship})</span>
                        <Button
                          type="button"
                          variant="outline"
                          size="sm"
                          onClick={() => handleRemoveParent(parent.id)}
                        >
                          Remove
                        </Button>
                      </div>
                    ))}
                    {selectedParents.length === 0 && (
                      <p className="text-sm text-gray-500">No parents/guardians assigned</p>
                    )}
                  </div>
                </div>

                {selectedParents.length < 2 && (
                  <div>
                    <Label htmlFor="addParent">Add Parent/Guardian</Label>
                    <Select onValueChange={handleAddParent}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select a parent/guardian" />
                      </SelectTrigger>
                      <SelectContent>
                        {parents
                          .filter(parent => !formData.parentIds.includes(parent.id))
                          .map((parent) => (
                            <SelectItem key={parent.id} value={parent.id}>
                              {parent.firstName} {parent.lastName} ({parent.relationship})
                            </SelectItem>
                          ))}
                      </SelectContent>
                    </Select>
                    <div className="mt-2 text-sm">
                      <Button
                        type="button"
                        variant="link"
                        className="p-0 h-auto text-school-blue"
                        onClick={() => navigate("/parents/new")}
                      >
                        + Create new parent/guardian
                      </Button>
                    </div>
                  </div>
                )}
              </CardContent>
            </Card>
          </div>
        </div>

        <div className="flex justify-end mt-6 space-x-4">
          <Button type="button" variant="outline" onClick={() => navigate("/pupils")}>
            Cancel
          </Button>
          <Button type="submit" className="bg-school-blue hover:bg-blue-600">
            {isEditMode ? "Update Pupil" : "Add Pupil"}
          </Button>
        </div>
      </form>
    </div>
  );
};

export default PupilForm;
