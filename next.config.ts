import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
  /* config options here */
  experimental: {
    ppr: 'incremental',
  },
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'www.adslzone.net',
        port: '',
        // pathname: '/account123/**',
      },
    ],
  },
  swcMinify: true,
};

export default nextConfig;
