import React, { useState, useEffect } from 'react';
import { TrendingUp, Clock, DollarSign, Target, Play, Pause } from 'lucide-react';

interface InvestmentPlan {
  id: string;
  name: string;
  minAmount: number;
  maxAmount: number;
  dailyReturn: number;
  duration: number;
  totalReturn: number;
  risk: 'low' | 'medium' | 'high';
  active: boolean;
}

interface UserInvestment {
  id: string;
  planId: string;
  planName: string;
  amount: number;
  startDate: Date;
  endDate: Date;
  dailyReturn: number;
  totalEarned: number;
  status: 'active' | 'completed' | 'paused';
  progress: number;
}

const InvestmentPlans: React.FC = () => {
  const [plans] = useState<InvestmentPlan[]>([
    {
      id: '1',
      name: 'Plano Iniciante',
      minAmount: 100,
      maxAmount: 1000,
      dailyReturn: 2.5,
      duration: 30,
      totalReturn: 75,
      risk: 'low',
      active: true
    },
    {
      id: '2',
      name: 'Plano Intermediário',
      minAmount: 1000,
      maxAmount: 5000,
      dailyReturn: 3.8,
      duration: 45,
      totalReturn: 171,
      risk: 'medium',
      active: true
    },
    {
      id: '3',
      name: 'Plano Avançado',
      minAmount: 5000,
      maxAmount: 20000,
      dailyReturn: 5.2,
      duration: 60,
      totalReturn: 312,
      risk: 'high',
      active: true
    }
  ]);

  const [userInvestments, setUserInvestments] = useState<UserInvestment[]>([
    {
      id: '1',
      planId: '1',
      planName: 'Plano Iniciante',
      amount: 500,
      startDate: new Date('2024-01-15'),
      endDate: new Date('2024-02-14'),
      dailyReturn: 2.5,
      totalEarned: 187.50,
      status: 'active',
      progress: 62
    },
    {
      id: '2',
      planId: '2',
      planName: 'Plano Intermediário',
      amount: 2000,
      startDate: new Date('2024-01-01'),
      endDate: new Date('2024-02-15'),
      dailyReturn: 3.8,
      totalEarned: 1140,
      status: 'active',
      progress: 33
    }
  ]);

  const [selectedPlan, setSelectedPlan] = useState<InvestmentPlan | null>(null);
  const [investmentAmount, setInvestmentAmount] = useState('');
  const [showInvestModal, setShowInvestModal] = useState(false);

  const getRiskColor = (risk: string) => {
    switch (risk) {
      case 'low': return 'text-green-400';
      case 'medium': return 'text-yellow-400';
      case 'high': return 'text-red-400';
      default: return 'text-gray-400';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'text-green-400';
      case 'completed': return 'text-blue-400';
      case 'paused': return 'text-yellow-400';
      default: return 'text-gray-400';
    }
  };

  const handleInvest = () => {
    if (!selectedPlan || !investmentAmount) return;

    const amount = parseFloat(investmentAmount);
    if (amount < selectedPlan.minAmount || amount > selectedPlan.maxAmount) {
      alert(`Valor deve estar entre $${selectedPlan.minAmount} e $${selectedPlan.maxAmount}`);
      return;
    }

    const newInvestment: UserInvestment = {
      id: Date.now().toString(),
      planId: selectedPlan.id,
      planName: selectedPlan.name,
      amount,
      startDate: new Date(),
      endDate: new Date(Date.now() + selectedPlan.duration * 24 * 60 * 60 * 1000),
      dailyReturn: selectedPlan.dailyReturn,
      totalEarned: 0,
      status: 'active',
      progress: 0
    };

    setUserInvestments([...userInvestments, newInvestment]);
    setShowInvestModal(false);
    setSelectedPlan(null);
    setInvestmentAmount('');
  };

  const totalInvested = userInvestments.reduce((sum, inv) => sum + inv.amount, 0);
  const totalEarned = userInvestments.reduce((sum, inv) => sum + inv.totalEarned, 0);
  const activeInvestments = userInvestments.filter(inv => inv.status === 'active').length;

  return (
    <div className="space-y-6">
      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-400 text-sm">Total Investido</p>
              <p className="text-2xl font-bold text-white">${totalInvested.toLocaleString()}</p>
            </div>
            <DollarSign className="w-8 h-8 text-[#2188B6]" />
          </div>
        </div>

        <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-400 text-sm">Total Ganho</p>
              <p className="text-2xl font-bold text-[#32FF7E]">${totalEarned.toLocaleString()}</p>
            </div>
            <TrendingUp className="w-8 h-8 text-[#32FF7E]" />
          </div>
        </div>

        <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-400 text-sm">Investimentos Ativos</p>
              <p className="text-2xl font-bold text-white">{activeInvestments}</p>
            </div>
            <Target className="w-8 h-8 text-[#2188B6]" />
          </div>
        </div>

        <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-400 text-sm">ROI Médio</p>
              <p className="text-2xl font-bold text-[#32FF7E]">
                {totalInvested > 0 ? ((totalEarned / totalInvested) * 100).toFixed(1) : '0.0'}%
              </p>
            </div>
            <TrendingUp className="w-8 h-8 text-[#32FF7E]" />
          </div>
        </div>
      </div>

      {/* Investment Plans */}
      <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
        <h2 className="text-xl font-bold text-white mb-6">Planos de Investimento</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {plans.map((plan) => (
            <div key={plan.id} className="bg-[#0D0D0D] p-6 rounded-lg border border-gray-700 hover:border-[#2188B6] transition-colors">
              <div className="flex justify-between items-start mb-4">
                <h3 className="text-lg font-semibold text-white">{plan.name}</h3>
                <span className={`text-sm font-medium ${getRiskColor(plan.risk)}`}>
                  {plan.risk.toUpperCase()}
                </span>
              </div>
              
              <div className="space-y-3 mb-6">
                <div className="flex justify-between">
                  <span className="text-gray-400">Valor Mínimo:</span>
                  <span className="text-white">${plan.minAmount.toLocaleString()}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-400">Valor Máximo:</span>
                  <span className="text-white">${plan.maxAmount.toLocaleString()}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-400">Retorno Diário:</span>
                  <span className="text-[#32FF7E]">{plan.dailyReturn}%</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-400">Duração:</span>
                  <span className="text-white">{plan.duration} dias</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-400">Retorno Total:</span>
                  <span className="text-[#32FF7E]">{plan.totalReturn}%</span>
                </div>
              </div>

              <button
                onClick={() => {
                  setSelectedPlan(plan);
                  setShowInvestModal(true);
                }}
                className="w-full bg-[#2188B6] hover:bg-[#1a6b8f] text-white py-2 px-4 rounded-lg transition-colors"
              >
                Investir Agora
              </button>
            </div>
          ))}
        </div>
      </div>

      {/* Active Investments */}
      <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800">
        <h2 className="text-xl font-bold text-white mb-6">Meus Investimentos</h2>
        <div className="space-y-4">
          {userInvestments.map((investment) => (
            <div key={investment.id} className="bg-[#0D0D0D] p-4 rounded-lg border border-gray-700">
              <div className="flex justify-between items-start mb-3">
                <div>
                  <h3 className="text-lg font-semibold text-white">{investment.planName}</h3>
                  <p className="text-gray-400">Investido: ${investment.amount.toLocaleString()}</p>
                </div>
                <span className={`text-sm font-medium ${getStatusColor(investment.status)}`}>
                  {investment.status.toUpperCase()}
                </span>
              </div>

              <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div>
                  <p className="text-gray-400 text-sm">Ganho Total</p>
                  <p className="text-[#32FF7E] font-semibold">${investment.totalEarned.toFixed(2)}</p>
                </div>
                <div>
                  <p className="text-gray-400 text-sm">Retorno Diário</p>
                  <p className="text-white">{investment.dailyReturn}%</p>
                </div>
                <div>
                  <p className="text-gray-400 text-sm">Data Início</p>
                  <p className="text-white">{investment.startDate.toLocaleDateString()}</p>
                </div>
                <div>
                  <p className="text-gray-400 text-sm">Data Fim</p>
                  <p className="text-white">{investment.endDate.toLocaleDateString()}</p>
                </div>
              </div>

              <div className="mb-3">
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-gray-400">Progresso</span>
                  <span className="text-white">{investment.progress}%</span>
                </div>
                <div className="w-full bg-gray-700 rounded-full h-2">
                  <div 
                    className="bg-[#32FF7E] h-2 rounded-full transition-all duration-300"
                    style={{ width: `${investment.progress}%` }}
                  ></div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Investment Modal */}
      {showInvestModal && selectedPlan && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800 w-full max-w-md">
            <h3 className="text-xl font-bold text-white mb-4">Investir em {selectedPlan.name}</h3>
            
            <div className="space-y-4 mb-6">
              <div>
                <label className="block text-gray-400 text-sm mb-2">Valor do Investimento</label>
                <input
                  type="number"
                  value={investmentAmount}
                  onChange={(e) => setInvestmentAmount(e.target.value)}
                  placeholder={`Min: $${selectedPlan.minAmount} - Max: $${selectedPlan.maxAmount}`}
                  className="w-full bg-[#0D0D0D] border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-[#2188B6] focus:outline-none"
                />
              </div>
              
              <div className="bg-[#0D0D0D] p-4 rounded-lg border border-gray-700">
                <h4 className="text-white font-semibold mb-2">Projeção de Retorno</h4>
                <div className="space-y-2 text-sm">
                  <div className="flex justify-between">
                    <span className="text-gray-400">Retorno Diário:</span>
                    <span className="text-[#32FF7E]">{selectedPlan.dailyReturn}%</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-400">Duração:</span>
                    <span className="text-white">{selectedPlan.duration} dias</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-400">Retorno Total:</span>
                    <span className="text-[#32FF7E]">{selectedPlan.totalReturn}%</span>
                  </div>
                  {investmentAmount && (
                    <div className="flex justify-between border-t border-gray-700 pt-2">
                      <span className="text-gray-400">Lucro Estimado:</span>
                      <span className="text-[#32FF7E] font-semibold">
                        ${((parseFloat(investmentAmount) * selectedPlan.totalReturn) / 100).toFixed(2)}
                      </span>
                    </div>
                  )}
                </div>
              </div>
            </div>

            <div className="flex space-x-3">
              <button
                onClick={() => setShowInvestModal(false)}
                className="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors"
              >
                Cancelar
              </button>
              <button
                onClick={handleInvest}
                className="flex-1 bg-[#2188B6] hover:bg-[#1a6b8f] text-white py-2 px-4 rounded-lg transition-colors"
              >
                Confirmar Investimento
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default InvestmentPlans;