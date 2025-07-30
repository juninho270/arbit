import React, { useState } from 'react';
import { Users, DollarSign, TrendingUp, Settings, Activity, Shield, Database, BarChart3, Coins, ToggleLeft, ToggleRight, Edit, UserCheck, Trash2, Plus, Save, X, Eye, EyeOff } from 'lucide-react';
import { getCryptocurrencies, updateCryptoArbitrageStatus } from '../../utils/api';
import { Cryptocurrency } from '../../types';
import { useAuth } from '../../contexts/AuthContext';

interface AdminStats {
  totalUsers: number;
  activeUsers: number;
  totalVolume: number;
  totalProfit: number;
  activeOperations: number;
  systemStatus: 'online' | 'maintenance' | 'offline';
}


const AdminDashboard: React.FC = () => {
  const { getAllUsers, updateUser, loginAsUser, addUser, deleteUser } = useAuth();
  const [activeTab, setActiveTab] = useState('overview');
  const [cryptos, setCryptos] = useState<Cryptocurrency[]>([]);
  const [loadingCryptos, setLoadingCryptos] = useState(false);
  const [editingUser, setEditingUser] = useState<string | null>(null);
  const [showAddUser, setShowAddUser] = useState(false);
  const [showPasswords, setShowPasswords] = useState<{ [key: string]: boolean }>({});
  
  const users = getAllUsers();
  
  const [stats] = useState<AdminStats>({
    totalUsers: users.length,
    activeUsers: users.filter(u => u.status === 'active').length,
    totalVolume: 2847392,
    totalProfit: 284739,
    activeOperations: 156,
    systemStatus: 'online'
  });

  const [newUser, setNewUser] = useState({
    name: '',
    email: '',
    balance: 0,
    botBalance: 0,
    role: 'user' as 'user' | 'admin',
    status: 'active' as 'active' | 'suspended' | 'pending'
  });

  const [systemSettings, setSystemSettings] = useState({
    arbitrageEnabled: true,
    botEnabled: true,
    minArbitrageAmount: 100,
    maxArbitrageAmount: 10000,
    arbitrageFee: 2.5,
    botActivationFee: 50,
    maintenanceMode: false
  });

  // Load cryptocurrencies when crypto tab is selected
  React.useEffect(() => {
    if (activeTab === 'cryptos') {
      loadCryptocurrencies();
    }
  }, [activeTab]);

  const loadCryptocurrencies = async () => {
    setLoadingCryptos(true);
    try {
      const data = await getCryptocurrencies(50); // Load more for admin panel
      setCryptos(data);
    } catch (error) {
      console.error('Error loading cryptocurrencies:', error);
    } finally {
      setLoadingCryptos(false);
    }
  };

  const handleToggleArbitrage = async (cryptoId: string, currentStatus: boolean) => {
    try {
      await updateCryptoArbitrageStatus(cryptoId, !currentStatus);
      // Reload the list to reflect changes
      await loadCryptocurrencies();
    } catch (error) {
      console.error('Error updating arbitrage status:', error);
    }
  };

  const handleEditUser = async (userId: string, field: string, value: any) => {
    const success = await updateUser(userId, { [field]: value });
    if (success) {
      console.log(`Updated ${field} for user ${userId}`);
    }
  };

  const handleLoginAsUser = (targetUser: any) => {
    if (window.confirm(`Tem certeza que deseja fazer login como ${targetUser.name}?`)) {
      loginAsUser(targetUser);
      // Navigate to user dashboard (the app will automatically redirect based on role)
      window.location.reload();
    }
  };

  const handleAddUser = async () => {
    if (!newUser.name || !newUser.email) {
      alert('Nome e email são obrigatórios');
      return;
    }
    
    const success = await addUser(newUser);
    if (success) {
      setNewUser({
        name: '',
        email: '',
        balance: 0,
        botBalance: 0,
        role: 'user',
        status: 'active'
      });
      setShowAddUser(false);
      alert('Usuário adicionado com sucesso!');
    } else {
      alert('Erro ao adicionar usuário');
    }
  };

  const handleDeleteUser = async (userId: string, userName: string) => {
    if (window.confirm(`Tem certeza que deseja excluir o usuário ${userName}? Esta ação não pode ser desfeita.`)) {
      const success = await deleteUser(userId);
      if (success) {
        alert('Usuário excluído com sucesso!');
      } else {
        alert('Erro ao excluir usuário');
      }
    }
  };
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'text-green-400';
      case 'suspended': return 'text-red-400';
      case 'pending': return 'text-yellow-400';
      case 'online': return 'text-green-400';
      case 'maintenance': return 'text-yellow-400';
      case 'offline': return 'text-red-400';
      default: return 'text-gray-400';
    }
  };

  const getStatusBg = (status: string) => {
    switch (status) {
      case 'active': return 'bg-green-400/10 border-green-400/20';
      case 'suspended': return 'bg-red-400/10 border-red-400/20';
      case 'pending': return 'bg-yellow-400/10 border-yellow-400/20';
      default: return 'bg-gray-400/10 border-gray-400/20';
    }
  };

  const tabs = [
    { id: 'overview', label: 'Visão Geral', icon: BarChart3 },
    { id: 'users', label: 'Usuários', icon: Users },
    { id: 'operations', label: 'Operações', icon: Activity },
    { id: 'cryptos', label: 'Criptomoedas', icon: Coins },
    { id: 'settings', label: 'Configurações', icon: Settings }
  ];

  return (
    <div className="space-y-6">
      <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
        <h1 className="text-2xl font-bold text-white mb-6">Painel Administrativo</h1>
        
        {/* Tabs */}
        <div className="flex space-x-1 mb-6 bg-[#0D0D0D] p-1 rounded-lg">
          {tabs.map((tab) => {
            const Icon = tab.icon;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center space-x-2 px-4 py-2 rounded-md transition-colors ${
                  activeTab === tab.id
                    ? 'bg-[#2188B6] text-white'
                    : 'text-gray-400 hover:text-white hover:bg-gray-700'
                }`}
              >
                <Icon className="w-4 h-4" />
                <span>{tab.label}</span>
              </button>
            );
          })}
        </div>

        {/* Overview Tab */}
        {activeTab === 'overview' && (
          <div className="space-y-6">
            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-gray-400 text-sm">Total de Usuários</p>
                    <p className="text-2xl font-bold text-white">{stats.totalUsers.toLocaleString()}</p>
                    <p className="text-green-400 text-sm">+12% este mês</p>
                  </div>
                  <Users className="w-8 h-8 text-[#2188B6]" />
                </div>
              </div>

              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-gray-400 text-sm">Usuários Ativos</p>
                    <p className="text-2xl font-bold text-white">{stats.activeUsers.toLocaleString()}</p>
                    <p className="text-green-400 text-sm">+8% esta semana</p>
                  </div>
                  <Activity className="w-8 h-8 text-[#32FF7E]" />
                </div>
              </div>

              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-gray-400 text-sm">Volume Total</p>
                    <p className="text-2xl font-bold text-white">${stats.totalVolume.toLocaleString()}</p>
                    <p className="text-green-400 text-sm">+15% este mês</p>
                  </div>
                  <DollarSign className="w-8 h-8 text-[#2188B6]" />
                </div>
              </div>

              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-gray-400 text-sm">Lucro Total</p>
                    <p className="text-2xl font-bold text-[#32FF7E]">${stats.totalProfit.toLocaleString()}</p>
                    <p className="text-green-400 text-sm">+22% este mês</p>
                  </div>
                  <TrendingUp className="w-8 h-8 text-[#32FF7E]" />
                </div>
              </div>
            </div>

            {/* System Status */}
            <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
              <h3 className="text-lg font-semibold text-white mb-4">Status do Sistema</h3>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="flex items-center space-x-3">
                  <div className={`w-3 h-3 rounded-full ${stats.systemStatus === 'online' ? 'bg-green-400' : 'bg-red-400'}`}></div>
                  <span className="text-white">Sistema Principal</span>
                  <span className={`text-sm ${getStatusColor(stats.systemStatus)}`}>
                    {stats.systemStatus.toUpperCase()}
                  </span>
                </div>
                <div className="flex items-center space-x-3">
                  <div className="w-3 h-3 rounded-full bg-green-400"></div>
                  <span className="text-white">API Externa</span>
                  <span className="text-green-400 text-sm">ONLINE</span>
                </div>
                <div className="flex items-center space-x-3">
                  <div className="w-3 h-3 rounded-full bg-green-400"></div>
                  <span className="text-white">Banco de Dados</span>
                  <span className="text-green-400 text-sm">ONLINE</span>
                </div>
              </div>
            </div>

            {/* Recent Activity */}
            <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
              <h3 className="text-lg font-semibold text-white mb-4">Atividade Recente</h3>
              <div className="space-y-3">
                <div className="flex items-center justify-between p-3 bg-[#1A1A1A] rounded-lg">
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 rounded-full bg-green-400"></div>
                    <span className="text-white">Novo usuário registrado</span>
                  </div>
                  <span className="text-gray-400 text-sm">2 min atrás</span>
                </div>
                <div className="flex items-center justify-between p-3 bg-[#1A1A1A] rounded-lg">
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 rounded-full bg-blue-400"></div>
                    <span className="text-white">Operação de arbitragem concluída</span>
                  </div>
                  <span className="text-gray-400 text-sm">5 min atrás</span>
                </div>
                <div className="flex items-center justify-between p-3 bg-[#1A1A1A] rounded-lg">
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 rounded-full bg-yellow-400"></div>
                    <span className="text-white">Bot ativado por usuário</span>
                  </div>
                  <span className="text-gray-400 text-sm">8 min atrás</span>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Users Tab */}
        {activeTab === 'users' && (
          <div className="space-y-6">
            <div className="flex justify-between items-center">
              <h2 className="text-xl font-semibold text-white">Gerenciamento de Usuários</h2>
              <div className="flex space-x-3">
                <input
                  type="text"
                  placeholder="Buscar usuários..."
                  className="bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                />
                <button className="bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-4 py-2 rounded-lg transition-colors">
                  Buscar
                </button>
              </div>
            </div>

            <div className="bg-[#0D0D0D] rounded-lg border border-gray-700 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-[#1A1A1A]">
                    <tr>
                      <th className="text-left p-4 text-gray-400 font-medium">Usuário</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Saldo Principal</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Saldo Bot</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Status</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Último Login</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    {users.map((user) => (
                      <tr key={user.id} className="border-t border-gray-700">
                        <td className="p-4">
                          <div>
                            <div className="text-white font-medium">{user.name}</div>
                            <div className="text-gray-400 text-sm">{user.email}</div>
                          </div>
                        </td>
                        <td className="p-4 text-white">${user.balance.toLocaleString()}</td>
                        <td className="p-4 text-white">${user.botBalance.toLocaleString()}</td>
                        <td className="p-4">
                          <span className={`px-2 py-1 rounded-full text-xs border ${getStatusBg(user.status)} ${getStatusColor(user.status)}`}>
                            {user.status.toUpperCase()}
                          </span>
                        </td>
                        <td className="p-4 text-gray-400">
                          {user.lastLogin ? new Date(user.lastLogin).toLocaleDateString('pt-BR') : 'Nunca'}
                        </td>
                        <td className="p-4">
                          <div className="flex space-x-2">
                            <button className="text-[#2188B6] hover:text-[#1a6b8f] text-sm">
                              Editar
                            </button>
                            <button className="text-red-400 hover:text-red-300 text-sm">
                              Suspender
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* Operations Tab */}
        {activeTab === 'operations' && (
          <div className="space-y-6">
            <h2 className="text-xl font-semibold text-white">Monitoramento de Operações</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <h3 className="text-white font-semibold mb-2">Operações Ativas</h3>
                <p className="text-3xl font-bold text-[#32FF7E]">{stats.activeOperations}</p>
                <p className="text-gray-400 text-sm">Em execução agora</p>
              </div>
              
              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <h3 className="text-white font-semibold mb-2">Operações Hoje</h3>
                <p className="text-3xl font-bold text-white">342</p>
                <p className="text-green-400 text-sm">+18% vs ontem</p>
              </div>
              
              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <h3 className="text-white font-semibold mb-2">Taxa de Sucesso</h3>
                <p className="text-3xl font-bold text-[#32FF7E]">94.2%</p>
                <p className="text-green-400 text-sm">+2.1% esta semana</p>
              </div>
            </div>

            <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
              <h3 className="text-lg font-semibold text-white mb-4">Operações Recentes</h3>
              <div className="space-y-3">
                {[1, 2, 3, 4, 5].map((i) => (
                  <div key={i} className="flex items-center justify-between p-3 bg-[#1A1A1A] rounded-lg">
                    <div className="flex items-center space-x-4">
                      <div className="w-10 h-10 bg-[#2188B6] rounded-full flex items-center justify-center">
                        <TrendingUp className="w-5 h-5 text-white" />
                      </div>
                      <div>
                        <div className="text-white font-medium">Arbitragem BTC/USDT</div>
                        <div className="text-gray-400 text-sm">João Silva • $1,250.00</div>
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-[#32FF7E] font-semibold">+$87.50</div>
                      <div className="text-gray-400 text-sm">2 min atrás</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* Cryptos Tab */}
        {activeTab === 'cryptos' && (
          <div className="space-y-6">
            <div className="flex justify-between items-center">
              <h2 className="text-xl font-semibold text-white">Gerenciamento de Criptomoedas</h2>
              <button
                onClick={loadCryptocurrencies}
                disabled={loadingCryptos}
                className="bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-4 py-2 rounded-lg transition-colors disabled:opacity-50"
              >
                {loadingCryptos ? 'Carregando...' : 'Atualizar Lista'}
              </button>
            </div>

            <div className="bg-[#0D0D0D] rounded-lg border border-gray-700 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-[#1A1A1A]">
                    <tr>
                      <th className="text-left p-4 text-gray-400 font-medium">Criptomoeda</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Preço Atual</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Variação 24h</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Arbitragem</th>
                      <th className="text-left p-4 text-gray-400 font-medium">Status/Motivo</th>
                    </tr>
                  </thead>
                  <tbody>
                    {cryptos.map((crypto) => (
                      <tr key={crypto.id} className="border-t border-gray-700">
                        <td className="p-4">
                          <div className="flex items-center space-x-3">
                            <img 
                              src={crypto.image} 
                              alt={crypto.name} 
                              className="w-8 h-8 rounded-full"
                            />
                            <div>
                              <div className="text-white font-medium">{crypto.name}</div>
                              <div className="text-gray-400 text-sm">{crypto.symbol.toUpperCase()}</div>
                            </div>
                          </div>
                        </td>
                        <td className="p-4 text-white font-mono">
                          ${crypto.current_price.toLocaleString('en-US', { 
                            minimumFractionDigits: 2,
                            maximumFractionDigits: crypto.current_price < 1 ? 6 : 2
                          })}
                        </td>
                        <td className="p-4">
                          <span className={`font-mono ${
                            crypto.price_change_percentage_24h > 0 ? 'text-green-400' : 'text-red-400'
                          }`}>
                            {crypto.price_change_percentage_24h > 0 ? '+' : ''}
                            {crypto.price_change_percentage_24h.toFixed(2)}%
                          </span>
                        </td>
                        <td className="p-4">
                          <button
                            onClick={() => handleToggleArbitrage(crypto.id, crypto.isArbitrageEnabled !== false)}
                            className="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded transition-colors"
                          >
                            {crypto.isArbitrageEnabled !== false ? (
                              <ToggleRight className="w-6 h-6 text-green-400" />
                            ) : (
                              <ToggleLeft className="w-6 h-6 text-red-400" />
                            )}
                            <span className={crypto.isArbitrageEnabled !== false ? 'text-green-400' : 'text-red-400'}>
                              {crypto.isArbitrageEnabled !== false ? 'Ativo' : 'Inativo'}
                            </span>
                          </button>
                        </td>
                        <td className="p-4">
                          {crypto.isArbitrageEnabled === false && crypto.deactivationReason ? (
                            <div className="text-yellow-400 text-sm">
                              <div className="font-medium">Desativado Automaticamente</div>
                              <div className="text-xs text-gray-400 mt-1">
                                {crypto.deactivationReason}
                              </div>
                            </div>
                          ) : (
                            <span className="text-green-400 text-sm">Disponível para Arbitragem</span>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            {cryptos.length === 0 && !loadingCryptos && (
              <div className="text-center py-8 text-gray-400">
                Nenhuma criptomoeda carregada. Clique em "Atualizar Lista" para carregar.
              </div>
            )}
          </div>
        )}

        {/* Settings Tab */}
        {activeTab === 'settings' && (
          <div className="space-y-6">
            <h2 className="text-xl font-semibold text-white">Configurações do Sistema</h2>
            
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* General Settings */}
              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <h3 className="text-lg font-semibold text-white mb-4">Configurações Gerais</h3>
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <div>
                      <label className="text-white font-medium">Arbitragem Habilitada</label>
                      <p className="text-gray-400 text-sm">Permitir operações de arbitragem</p>
                    </div>
                    <label className="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        checked={systemSettings.arbitrageEnabled}
                        onChange={(e) => setSystemSettings({ ...systemSettings, arbitrageEnabled: e.target.checked })}
                        className="sr-only peer"
                      />
                      <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                    </label>
                  </div>

                  <div className="flex items-center justify-between">
                    <div>
                      <label className="text-white font-medium">Bot Habilitado</label>
                      <p className="text-gray-400 text-sm">Permitir ativação de bots</p>
                    </div>
                    <label className="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        checked={systemSettings.botEnabled}
                        onChange={(e) => setSystemSettings({ ...systemSettings, botEnabled: e.target.checked })}
                        className="sr-only peer"
                      />
                      <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                    </label>
                  </div>

                  <div className="flex items-center justify-between">
                    <div>
                      <label className="text-white font-medium">Modo Manutenção</label>
                      <p className="text-gray-400 text-sm">Desabilitar acesso de usuários</p>
                    </div>
                    <label className="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        checked={systemSettings.maintenanceMode}
                        onChange={(e) => setSystemSettings({ ...systemSettings, maintenanceMode: e.target.checked })}
                        className="sr-only peer"
                      />
                      <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                    </label>
                  </div>
                </div>
              </div>

              {/* Financial Settings */}
              <div className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700">
                <h3 className="text-lg font-semibold text-white mb-4">Configurações Financeiras</h3>
                <div className="space-y-4">
                  <div>
                    <label className="block text-white font-medium mb-2">Valor Mínimo Arbitragem ($)</label>
                    <input
                      type="number"
                      value={systemSettings.minArbitrageAmount}
                      onChange={(e) => setSystemSettings({ ...systemSettings, minArbitrageAmount: parseFloat(e.target.value) })}
                      className="w-full bg-[#1A1A1A] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-white font-medium mb-2">Valor Máximo Arbitragem ($)</label>
                    <input
                      type="number"
                      value={systemSettings.maxArbitrageAmount}
                      onChange={(e) => setSystemSettings({ ...systemSettings, maxArbitrageAmount: parseFloat(e.target.value) })}
                      className="w-full bg-[#1A1A1A] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-white font-medium mb-2">Taxa de Arbitragem (%)</label>
                    <input
                      type="number"
                      step="0.1"
                      value={systemSettings.arbitrageFee}
                      onChange={(e) => setSystemSettings({ ...systemSettings, arbitrageFee: parseFloat(e.target.value) })}
                      className="w-full bg-[#1A1A1A] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-white font-medium mb-2">Taxa de Ativação do Bot ($)</label>
                    <input
                      type="number"
                      value={systemSettings.botActivationFee}
                      onChange={(e) => setSystemSettings({ ...systemSettings, botActivationFee: parseFloat(e.target.value) })}
                      className="w-full bg-[#1A1A1A] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                    />
                  </div>
                </div>
              </div>
            </div>

            <button className="bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-6 py-2 rounded-lg transition-colors">
              Salvar Configurações
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default AdminDashboard;