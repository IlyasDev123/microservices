'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useEffect, useState } from 'react';
import { api } from '@/lib/api';
import { removeToken } from '@/lib/auth';
import type { ApiResponse, User } from '@/lib/types';

const navItems = [
  { href: '/dashboard', label: 'Dashboard' },
  { href: '/profile', label: 'My Profile' },
  { href: '/users', label: 'Users' },
];

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const [user, setUser] = useState<Pick<User, 'name' | 'email'> | null>(null);

  useEffect(() => {
    api
      .get<ApiResponse<{ user: User }>>('/api/users/profile')
      .then((res) => setUser(res.data.user))
      .catch(() => {});
  }, []);

  const handleLogout = async () => {
    try {
      await api.post('/api/auth/logout', {});
    } catch {
      // ignore — remove token regardless
    }
    removeToken();
    router.push('/login');
  };

  return (
    <div className="flex h-screen bg-slate-50">
      {/* Sidebar */}
      <aside className="w-60 bg-slate-900 text-white flex flex-col shrink-0">
        <div className="px-5 py-4 border-b border-slate-700">
          <span className="text-base font-semibold tracking-tight">Microservices App</span>
        </div>

        <nav className="flex-1 px-3 py-4 space-y-0.5">
          {navItems.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className={`flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                pathname === item.href
                  ? 'bg-blue-600 text-white'
                  : 'text-slate-300 hover:bg-slate-800 hover:text-white'
              }`}
            >
              {item.label}
            </Link>
          ))}
        </nav>

        <div className="px-4 py-4 border-t border-slate-700">
          {user && (
            <div className="mb-3">
              <p className="text-xs font-medium text-white truncate">{user.name}</p>
              <p className="text-xs text-slate-400 truncate">{user.email}</p>
            </div>
          )}
          <button
            onClick={handleLogout}
            className="w-full px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg transition-colors text-left"
          >
            Sign out
          </button>
        </div>
      </aside>

      {/* Main */}
      <main className="flex-1 overflow-auto">
        <div className="p-8">{children}</div>
      </main>
    </div>
  );
}
