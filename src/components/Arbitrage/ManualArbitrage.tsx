import React, { useState, useEffect } from 'react';
import { Cryptocurrency, ArbitrageOperation } from '../../types';
import { getCryptocurrencies, simulateArbitrageProfit, simulateExecutionTime, findTransactionHash, getExplorerUrl, updateCryptoArbitrageStatus } from '../../utils/api';
import { useAuth } from '../../contexts/AuthContext';
import { TrendingUp, Play, Clock, CheckCircle, AlertCircle, Loader2 } from 'lucide-react';
import { v4 as uuidv4 } from 'uuid';

const ManualArbitrage: React.FC = () => {
  const { user, updateBalance } = useAuth();
  const [cryptos, setCryptos] = useState<Cryptocurrency[]>([]);
  const [selectedCrypto, setSelectedCrypto] = useState<Cryptocurrency | null>(null);
  const [amount, setAmount] = useState<number>(100);
  const [operation, setOperation] = useState<ArbitrageOperation | null>(null);
  const [isExecuting, setIsExecuting] = useState(false);
  const [cooldown, setCooldown] = useState<number>(0);

  useEffect(() => {
    loadCryptocurrencies();
    const interval = setInterval(loadCryptocurrencies, 300000); // Update every 5 minutes
    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    if (cooldown > 0) {
      const timer = setTimeout(() => setCooldown(cooldown - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [cooldown]);

  const loadCryptocurrencies = async () => {
    const data = await getCryptocurrencies(20);
    // Filter only enabled cryptocurrencies for arbitrage
    const enabledCryptos = data.filter(crypto => crypto.isArbitrageEnabled !== false);
    setCryptos(enabledCryptos);
    if (data.length > 0) {
      setSelectedCrypto(enabledCryptos[0] || null);
    }
  };

  const executeArbitrage = async () => {
    if (!selectedCrypto || !user || amount <= 0 || amount > user.balance || cooldown > 0) {
      return;
    }

    setIsExecuting(true);
    
    const newOperation: ArbitrageOperation = {
      id: uuidv4(),
      userId: user.id,
      type: 'manual',
      cryptocurrency: selectedCrypto.name,
      amount,
      buyPrice: selectedCrypto.current_price,
      sellPrice: selectedCrypto.current_price * 1.05, // Simulate 5% higher sell price
      profit: 0,
      profitPercentage: 0,
      status: 'pending',
      executionTime: simulateExecutionTime(),
      createdAt: new Date().toISOString()
    };

    const profit = simulateArbitrageProfit(amount);
    newOperation.profit = profit;
    newOperation.profitPercentage = (profit / amount) * 100;

    setOperation(newOperation);

    // Simulate execution time
    setTimeout(async () => {
      try {
        // Try to find a real transaction hash across multiple chains
        const transactionResult = await findTransactionHash(selectedCrypto.id, amount);
        
        if (!transactionResult) {
          // Cancel operation with user-friendly message
          const cancelledOperation: ArbitrageOperation = {
            ...newOperation,
            status: 'cancelled_no_hash',
            noHashReason: 'O sistema não encontrou transações possíveis para este token neste momento.',
            completedAt: new Date().toISOString()
          };

          setOperation(cancelledOperation);
          setIsExecuting(false);
          setCooldown(60);

          // Auto-deactivate the cryptocurrency for arbitrage
          await updateCryptoArbitrageStatus(
            selectedCrypto.id, 
            false, 
            `Auto-desativado: Falha em encontrar transações reais. Última tentativa: ${new Date().toLocaleString('pt-BR')} - Valor: $${amount.toFixed(2)}`
          );

          // Save cancelled operation to localStorage
          const savedOperations = JSON.parse(localStorage.getItem('operations') || '[]');
          savedOperations.unshift(cancelledOperation);
          localStorage.setItem('operations', JSON.stringify(savedOperations.slice(0, 50)));

          // Reload cryptocurrencies to update the list (remove deactivated one)
          loadCryptocurrencies();
          
          return;
        }
        
        // Operation successful with real transaction hash
        const completedOperation: ArbitrageOperation = {
          ...newOperation,
          status: 'completed',
          transactionHash: transactionResult.hash,
          chain: transactionResult.chain,
          completedAt: new Date().toISOString()
        };

        setOperation(completedOperation);
        updateBalance(user.balance + profit);
        setCooldown(60); // 1 minute cooldown
        setIsExecuting(false);

        // Save operation to localStorage (in real app, save to database)
        const savedOperations = JSON.parse(localStorage.getItem('operations') || '[]');
        savedOperations.unshift(completedOperation);
        localStorage.setItem('operations', JSON.stringify(savedOperations.slice(0, 50)));
        
      } catch (error) {
        setOperation({
          ...newOperation,
          status: 'failed',
          errorMessage: error instanceof Error ? error.message : 'Erro desconhecido'
        });
        setIsExecuting(false);
      }
    }, newOperation.executionTime);
  };

  const canExecute = user && amount > 0 && amount <= user.balance && cooldown === 0 && !isExecuting;

  return (
    <div className="space-y-6">
      <div className="bg-[#1A1A1A] rounded-lg border border-gray-800 p-6">
        <h3 className="text-[#E6E6E6] text-lg font-semibold mb-6">Arbitragem Manual</h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Criptomoeda
            </label>
            <select
              value={selectedCrypto?.id || ''}
              onChange={(e) => {
                const crypto = cryptos.find(c => c.id === e.target.value);
                setSelectedCrypto(crypto || null);
              }}
              className="w-full bg-[#0D0D0D] border border-gray-800 rounded-lg px-3 py-2 text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
            >
              {cryptos.map((crypto) => (
                <option key={crypto.id} value={crypto.id}>
                  {crypto.name} ({crypto.symbol.toUpperCase()}) - ${crypto.current_price.toFixed(2)}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Valor (USD)
            </label>
            <input
              type="number"
              value={amount}
              onChange={(e) => setAmount(Number(e.target.value))}
              max={user?.balance || 0}
              min={1}
              className="w-full bg-[#0D0D0D] border border-gray-800 rounded-lg px-3 py-2 text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
            />
            <p className="text-[#A6A6A6] text-xs mt-1">
              Saldo disponível: ${user?.balance.toFixed(2) || '0.00'}
            </p>
          </div>
        </div>

        {selectedCrypto && (
          <div className="mt-6 p-4 bg-[#0D0D0D] rounded-lg">
            <h4 className="text-[#E6E6E6] font-medium mb-3">Previsão da Operação</h4>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
              <div>
                <p className="text-[#A6A6A6]">Preço de Compra</p>
                <p className="text-[#E6E6E6] font-mono">${selectedCrypto.current_price.toFixed(4)}</p>
              </div>
              <div>
                <p className="text-[#A6A6A6]">Preço de Venda</p>
                <p className="text-[#E6E6E6] font-mono">${(selectedCrypto.current_price * 1.05).toFixed(4)}</p>
              </div>
              <div>
                <p className="text-[#A6A6A6]">Lucro Estimado</p>
                <p className="text-[#32FF7E] font-mono">$2.00 - $8.00</p>
              </div>
              <div>
                <p className="text-[#A6A6A6]">ROI Estimado</p>
                <p className="text-[#32FF7E] font-mono">2% - 8%</p>
              </div>
            </div>
          </div>
        )}

        <div className="mt-6 flex items-center justify-between">
          <button
            onClick={executeArbitrage}
            disabled={!canExecute}
            className={`flex items-center space-x-2 px-6 py-3 rounded-lg font-medium transition-colors ${
              canExecute
                ? 'bg-[#32FF7E] text-black hover:bg-[#32FF7E]/90'
                : 'bg-gray-600 text-gray-400 cursor-not-allowed'
            }`}
          >
            {isExecuting ? (
              <>
                <Loader2 className="animate-spin" size={20} />
                <span>Executando...</span>
              </>
            ) : (
              <>
                <Play size={20} />
                <span>Executar Arbitragem</span>
              </>
            )}
          </button>


          {operation && operation.status === 'failed' && operation.errorMessage && (
            <div className="mt-4 p-3 bg-[#FF4D4D]/20 border border-[#FF4D4D] rounded-lg">
              <p className="text-[#A6A6A6] text-sm mb-1">Erro:</p>
              <p className="text-[#FF4D4D] text-sm">{operation.errorMessage}</p>
            </div>
          )}
          {cooldown > 0 && (
            <div className="flex items-center space-x-2 text-[#FFD166]">
              <Clock size={16} />
              <span>Próxima operação em {cooldown}s</span>
            </div>
          )}
        </div>
      </div>

      {operation && (
        <div className="bg-[#1A1A1A] rounded-lg border border-gray-800 p-6">
          <div className="flex items-center space-x-3 mb-4">
            {operation.status === 'pending' && <Loader2 className="animate-spin text-[#FFD166]" size={20} />}
            {operation.status === 'completed' && <CheckCircle className="text-[#32FF7E]" size={20} />}
            {operation.status === 'failed' && <AlertCircle className="text-[#FF4D4D]" size={20} />}
            {operation.status === 'cancelled_no_hash' && <AlertCircle className="text-[#FFD166]" size={20} />}
            <h4 className="text-[#E6E6E6] font-medium">
              Status da Operação: {
                operation.status === 'pending' ? 'Executando' :
                operation.status === 'completed' ? 'Concluída' : 
                operation.status === 'cancelled_no_hash' ? 'Cancelada - Token Desativado' : 'Falhou'
              }
            </h4>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
              <p className="text-[#A6A6A6]">Criptomoeda</p>
              <p className="text-[#E6E6E6]">{operation.cryptocurrency}</p>
            </div>
            <div>
              <p className="text-[#A6A6A6]">Valor Investido</p>
              <p className="text-[#E6E6E6]">${operation.amount.toFixed(2)}</p>
            </div>
            <div>
              <p className="text-[#A6A6A6]">Lucro</p>
              <p className="text-[#32FF7E]">${operation.profit.toFixed(2)} ({operation.profitPercentage.toFixed(2)}%)</p>
            </div>
            <div>
              <p className="text-[#A6A6A6]">Tempo de Execução</p>
              <p className="text-[#E6E6E6]">{(operation.executionTime / 1000).toFixed(1)}s</p>
            </div>
          </div>

          {operation.status === 'completed' && operation.transactionHash && (
            <div className="mt-4 p-3 bg-[#0D0D0D] rounded-lg">
              <p className="text-[#A6A6A6] text-sm mb-1">Hash da Transação ({operation.chain?.toUpperCase()}):</p>
              <div className="flex items-center space-x-2">
                <code className="text-[#2188B6] font-mono text-sm">{operation.transactionHash}</code>
                <button
                  onClick={() => window.open(getExplorerUrl(operation.transactionHash!, operation.chain || 'bsc'), '_blank')}
                  className="text-[#2188B6] hover:text-[#E6E6E6] text-sm underline"
                >
                  Ver no Explorer
                </button>
              </div>
            </div>
          )}

          {operation.status === 'cancelled_no_hash' && (
            <div className="mt-4 p-3 bg-[#FF9500]/20 border border-[#FF9500] rounded-lg">
              <p className="text-[#A6A6A6] text-sm mb-1">Motivo do Cancelamento:</p>
              <p className="text-[#FF9500] text-sm">{operation.noHashReason}</p>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default ManualArbitrage;