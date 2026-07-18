export interface User {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  bio: string | null;
  avatar: string | null;
  status: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface UsersMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}
