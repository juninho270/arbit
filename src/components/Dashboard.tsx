import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { api } from '../utils/api';
import { TrendingUp, TrendingDown, DollarSign, Activity, Bot, Target } from 'lucide-react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

interface Stats {
  totalBalance: number;
  monthlyProfit: number;
  totalOperations: number;
  botStatus: string;
}

interface Operation {
  id: number;
  type: 'manual' | 'bot';
  cryptocurrency: string;
  amount: number;
  profit: number;
  profit_percentage: number;
  status: string;
  created_at: string;
  transaction_hash?: string;
}

const Dashboard: React.FC = () => {
  const { user, updateUser } = useAuth();
  const [stats, setStats] = useState<Stats>({
    totalBalance: 0,
    monthlyProfit: 0,
    totalOperations: 0,
    botStatus: 'inactive'
  });
  const [recentOperations, setRecentOperations] = useState<Operation[]>([]);
  const [loading, setLoading] = useState(true);

  // Mock data for the chart
  const chartData = [
    { name: 'Jan', value: 4000 },
    { name: 'Feb', value: 3000 },
    { name: 'Mar', value: 5000 },
    { name: 'Apr', value: 4500 },
    { name: 'May', value: 6000 },
    { name: 'Jun', value: 5500 },
  ];

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      // In a real app, these would be separate API calls
      const [operationsRes] = await Promise.all([
        api.get('/arbitrage/recent')
      ]);

      setRecentOperations(operationsRes.data);
      
      // Calculate stats from user data and operations
      if (user) {
        setStats({
          totalBalance: user.balance + user.bot_balance,
          monthlyProfit: operationsRes.data.reduce((sum: number, op: Operation) => sum + op.profit, 0),
          totalOperations: operationsRes.data.length,
          botStatus: 'active'
        });
      }
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="card">
          <div className="card-body">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <DollarSign className="h-8 w-8 text-green-400" />
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-400 truncate">Saldo Total</dt>
                  <dd className="text-lg font-medium text-white">
                    ${stats.totalBalance.toLocaleString()}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="card-body">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <TrendingUp className="h-8 w-8 text-blue-400" />
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-400 truncate">Lucro Mensal</dt>
                  <dd className="text-lg font-medium text-white">
                    ${stats.monthlyProfit.toLocaleString()}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="card-body">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <Activity className="h-8 w-8 text-purple-400" />
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-400 truncate">Operações</dt>
                  <dd className="text-lg font-medium text-white">{stats.totalOperations}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="card-body">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <Bot className="h-8 w-8 text-yellow-400" />
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-400 truncate">Bot Status</dt>
                  <dd className="text-lg font-medium text-white capitalize">{stats.botStatus}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Charts and Recent Operations */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Performance Chart */}
        <div className="card">
          <div className="card-header">
            <h3 className="text-lg font-medium text-white">Performance (Últimos 6 Meses)</h3>
          </div>
          <div className="card-body">
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={chartData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#374151" />
                <XAxis dataKey="name" stroke="#9CA3AF" />
                <YAxis stroke="#9CA3AF" />
                <Tooltip 
                  contentStyle={{ 
                    backgroundColor: '#1F2937', 
                    border: '1px solid #374151',
                    borderRadius: '0.5rem'
                  }}
                />
                <Line 
                  type="monotone" 
                  dataKey="value" 
                  stroke="#3B82F6" 
                  strokeWidth={2}
                  dot={{ fill: '#3B82F6' }}
                />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Recent Operations */}
        <div className="card">
          <div className="card-header">
            <h3 className="text-lg font-medium text-white">Operações Recentes</h3>
          </div>
          <div className="card-body">
            {recentOperations.length === 0 ? (
              <div className="text-center py-8">
                <Target className="mx-auto h-12 w-12 text-gray-400" />
                <h3 className="mt-2 text-sm font-medium text-gray-300">Nenhuma operação</h3>
                <p className="mt-1 text-sm text-gray-400">
                  Comece fazendo sua primeira arbitragem.
                </p>
              </div>
            ) : (
              <div className="space-y-4">
                {recentOperations.slice(0, 5).map((operation) => (
                  <div key={operation.id} className="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                    <div className="flex items-center">
                      <div className="flex-shrink-0">
                        {operation.type === 'bot' ? (
                          <Bot className="h-5 w-5 text-blue-400" />
                        ) : (
                          <Target className="h-5 w-5 text-green-400" />
                        )}
                      </div>
                      <div className="ml-3">
                        <p className="text-sm font-medium text-white">
                          {operation.cryptocurrency}
                        </p>
                        <p className="text-xs text-gray-400">
                          ${operation.amount.toLocaleString()} • {new Date(operation.created_at).toLocaleDateString()}
                        </p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-medium text-green-400">
                        +${operation.profit.toFixed(2)}
                      </p>
                      <p className="text-xs text-gray-400">
                        {operation.profit_percentage.toFixed(2)}%
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="card">
        <div className="card-header">
          <h3 className="text-lg font-medium text-white">Ações Rápidas</h3>
        </div>
        <div className="card-body">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <button className="btn btn-primary justify-center">
              <Target className="h-5 w-5" />
              Nova Arbitragem
            </button>
            <button className="btn btn-secondary justify-center">
              <Bot className="h-5 w-5" />
              Configurar Bot
            </button>
            <button className="btn btn-success justify-center">
              <DollarSign className="h-5 w-5" />
              Investir
            </button>
            <button className="btn btn-secondary justify-center">
              <TrendingUp className="h-5 w-5" />
              Ver Mercado
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;