'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { api } from '@/lib/api';
import type { ApiResponse, User } from '@/lib/types';

const statusColors: Record<string, string> = {
  active: 'bg-green-100 text-green-700',
  inactive: 'bg-gray-100 text-gray-600',
  suspended: 'bg-red-100 text-red-700',
};

export default function UserDetailPage() {
  const params = useParams();
  const router = useRouter();
  const userId = params.id as string;

  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [editing, setEditing] = useState(false);
  const [form, setForm] = useState({ name: '', phone: '', bio: '' });
  const [saving, setSaving] = useState(false);
  const [deleting, setDeleting] = useState(false);

  useEffect(() => {
    api
      .get<ApiResponse<{ user: User }>>(`/api/users/${userId}`)
      .then((res) => {
        setUser(res.data.user);
        setForm({
          name: res.data.user.name,
          phone: res.data.user.phone ?? '',
          bio: res.data.user.bio ?? '',
        });
      })
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'User not found');
      })
      .finally(() => setLoading(false));
  }, [userId]);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      const body: Record<string, string> = {};
      if (form.name) body.name = form.name;
      if (form.phone) body.phone = form.phone;
      if (form.bio) body.bio = form.bio;

      const res = await api.put<ApiResponse<{ user: User }>>(`/api/users/${userId}`, body);
      setUser(res.data.user);
      setEditing(false);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Update failed');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this user?')) return;
    setDeleting(true);
    try {
      await api.delete(`/api/users/${userId}`);
      router.push('/users');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Delete failed');
      setDeleting(false);
    }
  };

  const set = (key: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
    setForm((f) => ({ ...f, [key]: e.target.value }));

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600" />
      </div>
    );
  }

  if (error && !user) {
    return (
      <div className="max-w-xl">
        <div className="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
          {error}
        </div>
        <Link href="/users" className="mt-4 inline-block text-sm text-blue-600 hover:underline">
          ← Back to users
        </Link>
      </div>
    );
  }

  return (
    <div className="max-w-2xl">
      <div className="flex items-center gap-4 mb-6">
        <Link href="/users" className="text-sm text-gray-500 hover:text-gray-700">
          ← Users
        </Link>
        <h1 className="text-2xl font-bold text-gray-900">{user?.name}</h1>
      </div>

      {error && (
        <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
          {error}
        </div>
      )}

      {editing ? (
        <form
          onSubmit={handleSave}
          className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4"
        >
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
              type="text"
              value={form.name}
              onChange={set('name')}
              maxLength={255}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input
              type="text"
              value={form.phone}
              onChange={set('phone')}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea
              value={form.bio}
              onChange={set('bio')}
              rows={3}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
            />
          </div>
          <div className="flex gap-3">
            <button
              type="submit"
              disabled={saving}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm font-medium"
            >
              {saving ? 'Saving…' : 'Save'}
            </button>
            <button
              type="button"
              onClick={() => { setEditing(false); setError(''); }}
              className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium"
            >
              Cancel
            </button>
          </div>
        </form>
      ) : (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
          <Row label="ID" value={String(user?.id)} />
          <Row label="Name" value={user?.name} />
          <Row label="Email" value={user?.email} />
          <Row label="Phone" value={user?.phone ?? '—'} />
          <Row label="Bio" value={user?.bio ?? '—'} />
          <div>
            <dt className="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</dt>
            <span
              className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${
                statusColors[user?.status ?? 'active'] ?? 'bg-gray-100 text-gray-600'
              }`}
            >
              {user?.status}
            </span>
          </div>

          <div className="flex gap-3 pt-2 border-t border-gray-100">
            <button
              onClick={() => setEditing(true)}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"
            >
              Edit User
            </button>
            <button
              onClick={handleDelete}
              disabled={deleting}
              className="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 disabled:opacity-50 text-sm font-medium"
            >
              {deleting ? 'Deleting…' : 'Delete User'}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

function Row({ label, value }: { label: string; value?: string | null }) {
  return (
    <div>
      <dt className="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">{label}</dt>
      <dd className="text-sm text-gray-900">{value ?? '—'}</dd>
    </div>
  );
}
