import React, { useState } from 'react';
import { User, Lock, Bell, Shield, Eye, EyeOff, Save } from 'lucide-react';

interface UserProfile {
  name: string;
  email: string;
  phone: string;
  country: string;
  timezone: string;
}

interface SecuritySettings {
  twoFactorEnabled: boolean;
  emailNotifications: boolean;
  smsNotifications: boolean;
  loginAlerts: boolean;
}

const UserSettings: React.FC = () => {
  const [activeTab, setActiveTab] = useState('profile');
  const [showCurrentPassword, setShowCurrentPassword] = useState(false);
  const [showNewPassword, setShowNewPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const [profile, setProfile] = useState<UserProfile>({
    name: 'João Silva',
    email: 'joao@email.com',
    phone: '+55 11 99999-9999',
    country: 'Brasil',
    timezone: 'America/Sao_Paulo'
  });

  const [security, setSecurity] = useState<SecuritySettings>({
    twoFactorEnabled: false,
    emailNotifications: true,
    smsNotifications: false,
    loginAlerts: true
  });

  const [passwords, setPasswords] = useState({
    current: '',
    new: '',
    confirm: ''
  });

  const handleProfileSave = () => {
    // Simulate API call
    alert('Perfil atualizado com sucesso!');
  };

  const handlePasswordChange = () => {
    if (passwords.new !== passwords.confirm) {
      alert('As senhas não coincidem!');
      return;
    }
    if (passwords.new.length < 8) {
      alert('A nova senha deve ter pelo menos 8 caracteres!');
      return;
    }
    // Simulate API call
    alert('Senha alterada com sucesso!');
    setPasswords({ current: '', new: '', confirm: '' });
  };

  const handleSecuritySave = () => {
    // Simulate API call
    alert('Configurações de segurança atualizadas!');
  };

  const tabs = [
    { id: 'profile', label: 'Perfil', icon: User },
    { id: 'security', label: 'Segurança', icon: Shield },
    { id: 'password', label: 'Senha', icon: Lock },
    { id: 'notifications', label: 'Notificações', icon: Bell }
  ];

  return (
    <div className="space-y-6">
      <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
        <h1 className="text-2xl font-bold text-white mb-6">Configurações da Conta</h1>
        
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

        {/* Profile Tab */}
        {activeTab === 'profile' && (
          <div className="space-y-6">
            <h2 className="text-xl font-semibold text-white">Informações do Perfil</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-gray-400 text-sm mb-2">Nome Completo</label>
                <input
                  type="text"
                  value={profile.name}
                  onChange={(e) => setProfile({ ...profile, name: e.target.value })}
                  className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                />
              </div>
              
              <div>
                <label className="block text-gray-400 text-sm mb-2">Email</label>
                <input
                  type="email"
                  value={profile.email}
                  onChange={(e) => setProfile({ ...profile, email: e.target.value })}
                  className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                />
              </div>
              
              <div>
                <label className="block text-gray-400 text-sm mb-2">Telefone</label>
                <input
                  type="tel"
                  value={profile.phone}
                  onChange={(e) => setProfile({ ...profile, phone: e.target.value })}
                  className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                />
              </div>
              
              <div>
                <label className="block text-gray-400 text-sm mb-2">País</label>
                <select
                  value={profile.country}
                  onChange={(e) => setProfile({ ...profile, country: e.target.value })}
                  className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                >
                  <option value="Brasil">Brasil</option>
                  <option value="Estados Unidos">Estados Unidos</option>
                  <option value="Portugal">Portugal</option>
                  <option value="Argentina">Argentina</option>
                </select>
              </div>
            </div>

            <button
              onClick={handleProfileSave}
              className="flex items-center space-x-2 bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-6 py-2 rounded-lg transition-colors"
            >
              <Save className="w-4 h-4" />
              <span>Salvar Alterações</span>
            </button>
          </div>
        )}

        {/* Security Tab */}
        {activeTab === 'security' && (
          <div className="space-y-6">
            <h2 className="text-xl font-semibold text-white">Configurações de Segurança</h2>
            
            <div className="space-y-4">
              <div className="flex items-center justify-between p-4 bg-[#0D0D0D] rounded-lg border border-gray-700">
                <div>
                  <h3 className="text-white font-medium">Autenticação de Dois Fatores</h3>
                  <p className="text-gray-400 text-sm">Adicione uma camada extra de segurança à sua conta</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    checked={security.twoFactorEnabled}
                    onChange={(e) => setSecurity({ ...security, twoFactorEnabled: e.target.checked })}
                    className="sr-only peer"
                  />
                  <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                </label>
              </div>

              <div className="flex items-center justify-between p-4 bg-[#0D0D0D] rounded-lg border border-gray-700">
                <div>
                  <h3 className="text-white font-medium">Alertas de Login</h3>
                  <p className="text-gray-400 text-sm">Receba notificações sobre novos logins na sua conta</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    checked={security.loginAlerts}
                    onChange={(e) => setSecurity({ ...security, loginAlerts: e.target.checked })}
                    className="sr-only peer"
                  />
                  <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                </label>
              </div>
            </div>

            <button
              onClick={handleSecuritySave}
              className="flex items-center space-x-2 bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-6 py-2 rounded-lg transition-colors"
            >
              <Save className="w-4 h-4" />
              <span>Salvar Configurações</span>
            </button>
          </div>
        )}

        {/* Password Tab */}
        {activeTab === 'password' && (
          <div className="space-y-6">
            <h2 className="text-xl font-semibold text-white">Alterar Senha</h2>
            
            <div className="space-y-4 max-w-md">
              <div>
                <label className="block text-gray-400 text-sm mb-2">Senha Atual</label>
                <div className="relative">
                  <input
                    type={showCurrentPassword ? 'text' : 'password'}
                    value={passwords.current}
                    onChange={(e) => setPasswords({ ...passwords, current: e.target.value })}
                    className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 pr-10 text-white focus:border-[#2188B6] focus:outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => setShowCurrentPassword(!showCurrentPassword)}
                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                  >
                    {showCurrentPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>
              </div>

              <div>
                <label className="block text-gray-400 text-sm mb-2">Nova Senha</label>
                <div className="relative">
                  <input
                    type={showNewPassword ? 'text' : 'password'}
                    value={passwords.new}
                    onChange={(e) => setPasswords({ ...passwords, new: e.target.value })}
                    className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 pr-10 text-white focus:border-[#2188B6] focus:outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => setShowNewPassword(!showNewPassword)}
                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                  >
                    {showNewPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>
              </div>

              <div>
                <label className="block text-gray-400 text-sm mb-2">Confirmar Nova Senha</label>
                <div className="relative">
                  <input
                    type={showConfirmPassword ? 'text' : 'password'}
                    value={passwords.confirm}
                    onChange={(e) => setPasswords({ ...passwords, confirm: e.target.value })}
                    className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 pr-10 text-white focus:border-[#2188B6] focus:outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                  >
                    {showConfirmPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>
              </div>
            </div>

            <button
              onClick={handlePasswordChange}
              className="flex items-center space-x-2 bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-6 py-2 rounded-lg transition-colors"
            >
              <Lock className="w-4 h-4" />
              <span>Alterar Senha</span>
            </button>
          </div>
        )}

        {/* Notifications Tab */}
        {activeTab === 'notifications' && (
          <div className="space-y-6">
            <h2 className="text-xl font-semibold text-white">Preferências de Notificação</h2>
            
            <div className="space-y-4">
              <div className="flex items-center justify-between p-4 bg-[#0D0D0D] rounded-lg border border-gray-700">
                <div>
                  <h3 className="text-white font-medium">Notificações por Email</h3>
                  <p className="text-gray-400 text-sm">Receba atualizações sobre suas operações por email</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    checked={security.emailNotifications}
                    onChange={(e) => setSecurity({ ...security, emailNotifications: e.target.checked })}
                    className="sr-only peer"
                  />
                  <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                </label>
              </div>

              <div className="flex items-center justify-between p-4 bg-[#0D0D0D] rounded-lg border border-gray-700">
                <div>
                  <h3 className="text-white font-medium">Notificações por SMS</h3>
                  <p className="text-gray-400 text-sm">Receba alertas importantes via SMS</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    checked={security.smsNotifications}
                    onChange={(e) => setSecurity({ ...security, smsNotifications: e.target.checked })}
                    className="sr-only peer"
                  />
                  <div className="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2188B6]"></div>
                </label>
              </div>
            </div>

            <button
              onClick={handleSecuritySave}
              className="flex items-center space-x-2 bg-[#2188B6] hover:bg-[#1a6b8f] text-white px-6 py-2 rounded-lg transition-colors"
            >
              <Save className="w-4 h-4" />
              <span>Salvar Preferências</span>
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default UserSettings;