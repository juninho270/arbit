import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import LoginForm from './components/Auth/LoginForm';
import Sidebar from './components/Layout/Sidebar';
import Header from './components/Layout/Header';
import StatsCard from './components/Dashboard/StatsCard';
import PerformanceChart from './components/Dashboard/PerformanceChart';
import RecentOperations from './components/Dashboard/RecentOperations';
import CryptoList from './components/Market/CryptoList';
import ManualArbitrage from './components/Arbitrage/ManualArbitrage';
import BotDashboard from './components/Bot/BotDashboard';
import InvestmentPlans from './components/Investments/InvestmentPlans';
import UserSettings from './components/Settings/UserSettings';
import AdminDashboard from './components/Admin/AdminDashboard';
import { 
  Wallet, 
  TrendingUp, 
  Bot, 
  Target,
  Loader2
} from 'lucide-react';
import { ArbitrageOperation } from './types';

const Dashboard: React.FC = () => {
  const { user } = useAuth();
  const [operations, setOperations] = useState<ArbitrageOperation[]>([]);

  useEffect(() => {
    // Load operations from localStorage
    const savedOperations = JSON.parse(localStorage.getItem('operations') || '[]');
    setOperations(savedOperations);
  }, []);

  // Mock performance data
  const performanceData = Array.from({ length: 30 }, (_, i) => ({
    date: new Date(Date.now() - (29 - i) * 24 * 60 * 60 * 1000).toLocaleDateString('pt-BR'),
    profit: Math.random() * 100 + 50,
    manual: Math.random() * 50 + 20,
    bot: Math.random() * 50 + 30
  }));

  return (
    <div className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard
          title="Saldo Total"
          value={`$${((user?.balance || 0) + (user?.botBalance || 0)).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`}
          icon={Wallet}
          change="5.2%"
          changeType="positive"
          description="Saldo principal + bot"
        />
        <StatsCard
          title="Lucro do Mês"
          value="$1,245.80"
          icon={TrendingUp}
          change="12.3%"
          changeType="positive"
          description="Arbitragem + investimentos"
        />
        <StatsCard
          title="Operações"
          value={operations.length.toString()}
          icon={Target}
          change="8"
          changeType="positive"
          description="Total realizadas"
        />
        <StatsCard
          title="Bot Ativo"
          value="5h 32m"
          icon={Bot}
          change="Ativo"
          changeType="positive"
          description="Tempo de execução hoje"
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <PerformanceChart data={performanceData} />
        <RecentOperations operations={operations} />
      </div>
    </div>
  );
};

const AppContent: React.FC = () => {
  const { user, isLoading } = useAuth();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const toggleSidebar = () => {
    setSidebarOpen(!sidebarOpen);
  };

  // Close sidebar on route change for mobile
  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth >= 1024) {
        setSidebarOpen(false);
      }
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  if (isLoading) {
    return (
      <div className="min-h-screen bg-[#0D0D0D] flex items-center justify-center">
        <div className="flex items-center space-x-3">
          <Loader2 className="animate-spin text-[#2188B6]" size={32} />
          <span className="text-[#E6E6E6] text-lg">Carregando...</span>
        </div>
      </div>
    );
  }

  if (!user) {
    return <LoginForm />;
  }

  // Admin users only access admin panel
  if (user.role === 'admin') {
    return (
      <Router>
        <div className="min-h-screen bg-[#0D0D0D] flex">
          <Sidebar isOpen={sidebarOpen} onToggle={toggleSidebar} />
          
          <div className="flex-1 flex flex-col lg:ml-0">
            <Header onMenuToggle={toggleSidebar} />
            
            <main className="flex-1 p-4 lg:p-6">
              <Routes>
                <Route path="/admin" element={<AdminDashboard />} />
                <Route path="*" element={<Navigate to="/admin" replace />} />
              </Routes>
            </main>
          </div>
        </div>
      </Router>
    );
  }

  // Regular users access user features only
  return (
    <Router>
      <div className="min-h-screen bg-[#0D0D0D] flex">
        <Sidebar isOpen={sidebarOpen} onToggle={toggleSidebar} />
        
        <div className="flex-1 flex flex-col lg:ml-0">
          <Header onMenuToggle={toggleSidebar} />
          
          <main className="flex-1 p-4 lg:p-6">
            <Routes>
              <Route path="/" element={<Dashboard />} />
              <Route path="/market" element={<CryptoList />} />
              <Route path="/arbitrage" element={<ManualArbitrage />} />
              <Route path="/bot" element={<BotDashboard />} />
              <Route path="/investments" element={<InvestmentPlans />} />
              <Route path="/settings" element={<UserSettings />} />
              <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
          </main>
        </div>
      </div>
    </Router>
  );
};

const App: React.FC = () => {
  return (
    <AuthProvider>
      <AppContent />
    </AuthProvider>
  );
};

export default App;