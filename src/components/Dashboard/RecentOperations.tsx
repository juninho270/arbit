import React from 'react';
import { ArbitrageOperation } from '../../types';
import { getExplorerUrl } from '../../utils/api';
import { Clock, TrendingUp, Bot, User, ExternalLink } from 'lucide-react';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

interface RecentOperationsProps {
  operations: ArbitrageOperation[];
}

const RecentOperations: React.FC<RecentOperationsProps> = ({ operations }) => {
  const getStatusColor = (status: ArbitrageOperation['status']) => {
    switch (status) {
      case 'completed':
        return 'text-[#32FF7E] bg-[#32FF7E]/20';
      case 'pending':
        return 'text-[#FFD166] bg-[#FFD166]/20';
      case 'failed':
        return 'text-[#FF4D4D] bg-[#FF4D4D]/20';
      case 'cancelled_no_hash':
        return 'text-[#FF9500] bg-[#FF9500]/20';
      default:
        return 'text-[#A6A6A6] bg-[#A6A6A6]/20';
    }
  };

  const getStatusText = (status: ArbitrageOperation['status']) => {
    switch (status) {
      case 'completed':
        return 'Concluída';
      case 'pending':
        return 'Pendente';
      case 'failed':
        return 'Falhou';
      case 'cancelled_no_hash':
        return 'Cancelada';
      default:
        return 'Desconhecido';
    }
  };

  return (
    <div className="bg-[#1A1A1A] rounded-lg border border-gray-800">
      <div className="p-6 border-b border-gray-800">
        <h3 className="text-[#E6E6E6] text-lg font-semibold">Operações Recentes</h3>
      </div>
      
      <div className="p-6">
        {operations.length === 0 ? (
          <div className="text-center py-8">
            <TrendingUp className="mx-auto mb-4 text-[#A6A6A6]" size={48} />
            <p className="text-[#A6A6A6]">Nenhuma operação realizada ainda</p>
          </div>
        ) : (
          <div className="space-y-4">
            {operations.map((operation) => (
              <div key={operation.id} className="flex items-center space-x-4 p-4 bg-[#0D0D0D] rounded-lg">
                <div className="flex-shrink-0">
                  {operation.type === 'bot' ? (
                    <Bot className="text-[#2188B6]" size={20} />
                  ) : (
                    <User className="text-[#A6A6A6]" size={20} />
                  )}
                </div>
                
                <div className="flex-1 min-w-0">
                  <div className="flex items-center space-x-2 mb-1">
                    <p className="text-[#E6E6E6] font-medium">{operation.cryptocurrency}</p>
                    <span className={`px-2 py-1 rounded-full text-xs ${getStatusColor(operation.status)}`}>
                      {getStatusText(operation.status)}
                    </span>
                  </div>
                  
                  <div className="flex items-center space-x-4 text-sm text-[#A6A6A6]">
                    <span>Valor: ${operation.amount.toFixed(2)}</span>
                    <span className="text-[#32FF7E]">
                      Lucro: ${operation.profit.toFixed(2)} ({operation.profitPercentage.toFixed(2)}%)
                    </span>
                    <div className="flex items-center space-x-1">
                      <Clock size={14} />
                      <span>{format(new Date(operation.createdAt), 'dd/MM HH:mm', { locale: ptBR })}</span>
                    </div>
                    {operation.transactionHash && (
                      <a
                        href={getExplorerUrl(operation.transactionHash, operation.chain || 'bsc')}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-[#2188B6] hover:text-[#1a6b8f] transition-colors"
                        title="Ver transação no Explorer"
                      >
                        <ExternalLink className="w-4 h-4" />
                      </a>
                    )}
                  </div>
                </div>
                
                {operation.transactionHash && (
                  <button 
                    className="flex-shrink-0 p-2 text-[#2188B6] hover:text-[#E6E6E6] transition-colors"
                    onClick={() => window.open(getExplorerUrl(operation.transactionHash!, operation.chain || 'bsc'), '_blank')}
                    title="Ver no Explorer"
                  >
                    <ExternalLink size={16} />
                  </button>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default RecentOperations;