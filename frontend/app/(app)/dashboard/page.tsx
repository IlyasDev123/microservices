'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { api } from '@/lib/api';
import type { ApiResponse, User, UsersMeta } from '@/lib/types';

export default function DashboardPage() {
  const [user, setUser] = useState<User | null>(null);
  const [totalUsers, setTotalUsers] = useState<number | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      api.get<ApiResponse<{ user: User }>>('/api/users/profile'),
      api.get<ApiResponse<{ users: User[]; meta: UsersMeta }>>('/api/users'),
    ])
      .then(([profileRes, usersRes]) => {
        setUser(profileRes.data.user);
        setTotalUsers(usersRes.data.meta.total);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600" />
      </div>
    );
  }

  return (
    <div>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-gray-900">
          Welcome back, {user?.name ?? 'User'}!
        </h1>
        <p className="text-gray-500 mt-1 text-sm">{user?.email}</p>
      </div>

      <div className="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <p className="text-3xl font-bold text-blue-600">{totalUsers ?? '—'}</p>
          <p className="text-sm text-gray-500 mt-1">Total Users</p>
        </div>

        <Link
          href="/profile"
          className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:border-blue-300 hover:shadow-md transition-all group"
        >
          <p className="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
            My Profile
          </p>
          <p className="text-sm text-gray-500 mt-1">View and update your profile</p>
        </Link>

        <Link
          href="/users"
          className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:border-blue-300 hover:shadow-md transition-all group"
        >
          <p className="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
            Manage Users
          </p>
          <p className="text-sm text-gray-500 mt-1">Browse and manage all users</p>
        </Link>
      </div>
    </div>
  );
}
