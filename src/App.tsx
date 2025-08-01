import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import { useAuth } from './contexts/AuthContext';
import Login from './components/Login';
import Dashboard from './components/Dashboard';
import Market from './components/Market';
import Arbitrage from './components/Arbitrage';
import Bot from './components/Bot';
import Investments from './components/Investments';
import Settings from './components/Settings';
import AdminDashboard from './components/admin/AdminDashboard';
import AdminUsers from './components/admin/AdminUsers';
import AdminOperations from './components/admin/AdminOperations';
import AdminCryptos from './components/admin/AdminCryptos';
import AdminSettings from './components/admin/AdminSettings';
import Layout from './components/Layout';

const ProtectedRoute: React.FC<{ children: React.ReactNode; adminOnly?: boolean }> = ({ 
  children, 
  adminOnly = false 
}) => {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-900 flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (adminOnly && user.role !== 'admin') {
    return <Navigate to="/dashboard" replace />;
  }

  return <>{children}</>;
};

const AppRoutes: React.FC = () => {
  const { user } = useAuth();

  return (
    <Routes>
      <Route path="/login" element={user ? <Navigate to={user.role === 'admin' ? '/admin' : '/dashboard'} replace /> : <Login />} />
      
      <Route path="/" element={
        <ProtectedRoute>
          <Navigate to={user?.role === 'admin' ? '/admin' : '/dashboard'} replace />
        </ProtectedRoute>
      } />

      {/* User Routes */}
      <Route path="/dashboard" element={
        <ProtectedRoute>
          <Layout><Dashboard /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/market" element={
        <ProtectedRoute>
          <Layout><Market /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/arbitrage" element={
        <ProtectedRoute>
          <Layout><Arbitrage /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/bot" element={
        <ProtectedRoute>
          <Layout><Bot /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/investments" element={
        <ProtectedRoute>
          <Layout><Investments /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/settings" element={
        <ProtectedRoute>
          <Layout><Settings /></Layout>
        </ProtectedRoute>
      } />

      {/* Admin Routes */}
      <Route path="/admin" element={
        <ProtectedRoute adminOnly>
          <Layout><AdminDashboard /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/admin/users" element={
        <ProtectedRoute adminOnly>
          <Layout><AdminUsers /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/admin/operations" element={
        <ProtectedRoute adminOnly>
          <Layout><AdminOperations /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/admin/cryptos" element={
        <ProtectedRoute adminOnly>
          <Layout><AdminCryptos /></Layout>
        </ProtectedRoute>
      } />
      
      <Route path="/admin/settings" element={
        <ProtectedRoute adminOnly>
          <Layout><AdminSettings /></Layout>
        </ProtectedRoute>
      } />
    </Routes>
  );
};

function App() {
  return (
    <AuthProvider>
      <Router>
        <AppRoutes />
      </Router>
    </AuthProvider>
  );
}

export default App;