import React, { useState, useEffect } from 'react';
import { BotSettings } from '../../types';
import { useAuth } from '../../contexts/AuthContext';
import { Bot, Power, Settings, TrendingUp, Pause, Play, AlertCircle } from 'lucide-react';

const BotDashboard: React.FC = () => {
  const { user } = useAuth();
  const [botSettings, setBotSettings] = useState<BotSettings>({
    id: '1',
    userId: user?.id || '',
    isActive: false,
    minProfit: 2,
    maxAmount: 1000,
    interval: 300, // 5 minutes
    selectedCoins: ['bitcoin', 'ethereum'],
    autoReinvest: true,
    stopLoss: 5,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  });

  const [statistics, setStatistics] = useState({
    totalOperations: 0,
    totalProfit: 0,
    successRate: 0,
    averageProfit: 0,
    uptime: '0h 0m'
  });

  const toggleBot = () => {
    setBotSettings(prev => ({
      ...prev,
      isActive: !prev.isActive,
      updatedAt: new Date().toISOString()
    }));
  };

  const updateSettings = (newSettings: Partial<BotSettings>) => {
    setBotSettings(prev => ({
      ...prev,
      ...newSettings,
      updatedAt: new Date().toISOString()
    }));
  };

  return (
    <div className="space-y-6">
      {/* Bot Status Card */}
      <div className="bg-[#1A1A1A] rounded-lg border border-gray-800 p-6">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center space-x-3">
            <Bot className="text-[#2188B6]" size={24} />
            <h3 className="text-[#E6E6E6] text-lg font-semibold">Bot de Arbitragem</h3>
          </div>
          
          <div className="flex items-center space-x-3">
            <div className={`flex items-center space-x-2 px-3 py-1 rounded-full ${
              botSettings.isActive ? 'bg-[#32FF7E]/20 text-[#32FF7E]' : 'bg-[#FF4D4D]/20 text-[#FF4D4D]'
            }`}>
              <div className={`w-2 h-2 rounded-full ${
                botSettings.isActive ? 'bg-[#32FF7E] animate-pulse' : 'bg-[#FF4D4D]'
              }`}></div>
              <span className="text-sm font-medium">
                {botSettings.isActive ? 'Ativo' : 'Inativo'}
              </span>
            </div>
            
            <button
              onClick={toggleBot}
              className={`flex items-center space-x-2 px-4 py-2 rounded-lg font-medium transition-colors ${
                botSettings.isActive
                  ? 'bg-[#FF4D4D] text-white hover:bg-[#FF4D4D]/90'
                  : 'bg-[#32FF7E] text-black hover:bg-[#32FF7E]/90'
              }`}
            >
              {botSettings.isActive ? <Pause size={16} /> : <Play size={16} />}
              <span>{botSettings.isActive ? 'Pausar' : 'Iniciar'}</span>
            </button>
          </div>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div className="bg-[#0D0D0D] p-4 rounded-lg">
            <p className="text-[#A6A6A6] text-sm">Saldo do Bot</p>
            <p className="text-[#E6E6E6] text-xl font-bold">
              ${user?.botBalance.toFixed(2) || '0.00'}
            </p>
          </div>
          
          <div className="bg-[#0D0D0D] p-4 rounded-lg">
            <p className="text-[#A6A6A6] text-sm">Lucro Total</p>
            <p className="text-[#32FF7E] text-xl font-bold">
              ${statistics.totalProfit.toFixed(2)}
            </p>
          </div>
          
          <div className="bg-[#0D0D0D] p-4 rounded-lg">
            <p className="text-[#A6A6A6] text-sm">Taxa de Sucesso</p>
            <p className="text-[#E6E6E6] text-xl font-bold">
              {statistics.successRate.toFixed(1)}%
            </p>
          </div>
          
          <div className="bg-[#0D0D0D] p-4 rounded-lg">
            <p className="text-[#A6A6A6] text-sm">Tempo Ativo</p>
            <p className="text-[#E6E6E6] text-xl font-bold">
              {statistics.uptime}
            </p>
          </div>
        </div>
      </div>

      {/* Bot Configuration */}
      <div className="bg-[#1A1A1A] rounded-lg border border-gray-800 p-6">
        <div className="flex items-center space-x-3 mb-6">
          <Settings className="text-[#2188B6]" size={20} />
          <h4 className="text-[#E6E6E6] font-medium">Configurações do Bot</h4>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Lucro Mínimo (%)
            </label>
            <input
              type="number"
              value={botSettings.minProfit}
              onChange={(e) => updateSettings({ minProfit: Number(e.target.value) })}
              min={0.1}
              max={10}
              step={0.1}
              className="w-full bg-[#0D0D0D] border border-gray-800 rounded-lg px-3 py-2 text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
            />
          </div>

          <div>
            <label className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Valor Máximo por Operação
            </label>
            <input
              type="number"
              value={botSettings.maxAmount}
              onChange={(e) => updateSettings({ maxAmount: Number(e.target.value) })}
              min={10}
              max={user?.botBalance || 0}
              className="w-full bg-[#0D0D0D] border border-gray-800 rounded-lg px-3 py-2 text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
            />
          </div>

          <div>
            <label className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Intervalo entre Operações (segundos)
            </label>
            <select
              value={botSettings.interval}
              onChange={(e) => updateSettings({ interval: Number(e.target.value) })}
              className="w-full bg-[#0D0D0D] border border-gray-800 rounded-lg px-3 py-2 text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
            >
              <option value={60}>1 minuto</option>
              <option value={300}>5 minutos</option>
              <option value={600}>10 minutos</option>
              <option value={1800}>30 minutos</option>
              <option value={3600}>1 hora</option>
            </select>
          </div>

          <div>
            <label className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Stop Loss (%)
            </label>
            <input
              type="number"
              value={botSettings.stopLoss}
              onChange={(e) => updateSettings({ stopLoss: Number(e.target.value) })}
              min={1}
              max={20}
              className="w-full bg-[#0D0D0D] border border-gray-800 rounded-lg px-3 py-2 text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
            />
          </div>
        </div>

        <div className="mt-6">
          <label className="flex items-center space-x-3">
            <input
              type="checkbox"
              checked={botSettings.autoReinvest}
              onChange={(e) => updateSettings({ autoReinvest: e.target.checked })}
              className="w-4 h-4 text-[#2188B6] bg-[#0D0D0D] border-gray-800 rounded focus:ring-[#2188B6] focus:ring-2"
            />
            <span className="text-[#E6E6E6]">Reinvestir lucros automaticamente</span>
          </label>
        </div>
      </div>

      {/* Recent Bot Operations */}
      <div className="bg-[#1A1A1A] rounded-lg border border-gray-800 p-6">
        <div className="flex items-center space-x-3 mb-6">
          <TrendingUp className="text-[#2188B6]" size={20} />
          <h4 className="text-[#E6E6E6] font-medium">Operações Recentes do Bot</h4>
        </div>

        <div className="space-y-3">
          {[1, 2, 3].map((_, index) => (
            <div key={index} className="flex items-center justify-between p-3 bg-[#0D0D0D] rounded-lg">
              <div className="flex items-center space-x-3">
                <Bot className="text-[#2188B6]" size={16} />
                <div>
                  <p className="text-[#E6E6E6] text-sm font-medium">Bitcoin (BTC)</p>
                  <p className="text-[#A6A6A6] text-xs">Há 2 horas</p>
                </div>
              </div>
              
              <div className="text-right">
                <p className="text-[#32FF7E] text-sm font-medium">+$24.50</p>
                <p className="text-[#A6A6A6] text-xs">2.45%</p>
              </div>
            </div>
          ))}
        </div>

        {statistics.totalOperations === 0 && (
          <div className="text-center py-8">
            <AlertCircle className="mx-auto mb-2 text-[#A6A6A6]" size={24} />
            <p className="text-[#A6A6A6]">Nenhuma operação realizada ainda</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default BotDashboard;