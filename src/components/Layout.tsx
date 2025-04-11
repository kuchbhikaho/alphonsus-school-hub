
import { NavLink, Outlet } from 'react-router-dom';
import { BookOpen, Users, User, GraduationCap, LayoutDashboard } from 'lucide-react';

const Layout = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-school-blue text-white shadow-md">
        <div className="container mx-auto py-4 px-6">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold">St Alphonsus Primary School</h1>
          </div>
        </div>
      </header>
      
      <div className="container mx-auto flex flex-col md:flex-row">
        <nav className="w-full md:w-64 bg-white shadow-md p-4 md:min-h-[calc(100vh-64px)]">
          <ul className="space-y-2">
            <li>
              <NavLink
                to="/"
                className={({ isActive }) =>
                  `flex items-center gap-2 p-2 rounded ${
                    isActive ? 'bg-school-blue text-white' : 'hover:bg-gray-100'
                  }`
                }
              >
                <LayoutDashboard size={20} />
                <span>Dashboard</span>
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/pupils"
                className={({ isActive }) =>
                  `flex items-center gap-2 p-2 rounded ${
                    isActive ? 'bg-school-blue text-white' : 'hover:bg-gray-100'
                  }`
                }
              >
                <Users size={20} />
                <span>Pupils</span>
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/teachers"
                className={({ isActive }) =>
                  `flex items-center gap-2 p-2 rounded ${
                    isActive ? 'bg-school-blue text-white' : 'hover:bg-gray-100'
                  }`
                }
              >
                <User size={20} />
                <span>Teachers</span>
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/classes"
                className={({ isActive }) =>
                  `flex items-center gap-2 p-2 rounded ${
                    isActive ? 'bg-school-blue text-white' : 'hover:bg-gray-100'
                  }`
                }
              >
                <BookOpen size={20} />
                <span>Classes</span>
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/parents"
                className={({ isActive }) =>
                  `flex items-center gap-2 p-2 rounded ${
                    isActive ? 'bg-school-blue text-white' : 'hover:bg-gray-100'
                  }`
                }
              >
                <GraduationCap size={20} />
                <span>Parents</span>
              </NavLink>
            </li>
          </ul>
        </nav>
        
        <main className="flex-1 p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default Layout;
