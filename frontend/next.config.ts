import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
  async rewrites() {
    const authUrl = process.env.AUTH_SERVICE_URL ?? 'http://localhost:8001';
    const userUrl = process.env.USER_SERVICE_URL ?? 'http://localhost:8002';

    return [
      {
        source: '/api/auth/:path*',
        destination: `${authUrl}/api/v1/auth/:path*`,
      },
      {
        source: '/api/users/:path*',
        destination: `${userUrl}/api/v1/users/:path*`,
      },
    ];
  },
};

export default nextConfig;
