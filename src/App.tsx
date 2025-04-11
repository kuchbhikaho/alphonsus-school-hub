
import { Toaster as ShadcnToaster } from "@/components/ui/toaster";
import { Toaster as SonnerToaster } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Layout from "./components/Layout";
import Dashboard from "./pages/Dashboard";
import NotFound from "./pages/NotFound";
import Pupils from "./pages/Pupils";
import PupilForm from "./pages/PupilForm";
import Teachers from "./pages/Teachers";
import TeacherForm from "./pages/TeacherForm";
import Classes from "./pages/Classes";
import ClassForm from "./pages/ClassForm";
import Parents from "./pages/Parents";
import ParentForm from "./pages/ParentForm";
import ClassPupils from "./pages/ClassPupils";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <BrowserRouter>
      <TooltipProvider>
        <Routes>
          <Route path="/" element={<Layout />}>
            <Route index element={<Dashboard />} />
            <Route path="pupils" element={<Pupils />} />
            <Route path="pupils/new" element={<PupilForm />} />
            <Route path="pupils/:id/edit" element={<PupilForm />} />
            <Route path="teachers" element={<Teachers />} />
            <Route path="teachers/new" element={<TeacherForm />} />
            <Route path="teachers/:id/edit" element={<TeacherForm />} />
            <Route path="classes" element={<Classes />} />
            <Route path="classes/new" element={<ClassForm />} />
            <Route path="classes/:id/edit" element={<ClassForm />} />
            <Route path="classes/:id/pupils" element={<ClassPupils />} />
            <Route path="parents" element={<Parents />} />
            <Route path="parents/new" element={<ParentForm />} />
            <Route path="parents/:id/edit" element={<ParentForm />} />
          </Route>
          <Route path="*" element={<NotFound />} />
        </Routes>
        <ShadcnToaster />
        <SonnerToaster />
      </TooltipProvider>
    </BrowserRouter>
  </QueryClientProvider>
);

export default App;
