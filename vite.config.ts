import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/api/coingecko': {
        target: 'https://api.coingecko.com',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/coingecko/, '')
      },
      '/api/bscscan': {
        target: 'https://api.bscscan.com',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/bscscan/, '')
      },
      '/api/moralis': {
        target: 'https://deep-index.moralis.io',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/moralis/, '')
      }
    }
  },
  optimizeDeps: {
    exclude: ['lucide-react'],
  },
});
