import React from 'react';
import { Bell, Wallet, TrendingUp, TrendingDown, Menu } from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';

interface HeaderProps {
  onMenuToggle: () => void;
}

const Header: React.FC<HeaderProps> = ({ onMenuToggle }) => {
  const { user } = useAuth();

  return (
    <header className="bg-[#1A1A1A] border-b border-gray-800 p-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-6">
          {/* Mobile Menu Button */}
          <button
            onClick={onMenuToggle}
            className="lg:hidden text-[#A6A6A6] hover:text-[#E6E6E6] transition-colors"
          >
            <Menu size={24} />
          </button>
          
          <div className="flex items-center space-x-2">
            <Wallet className="text-[#2188B6]" size={20} />
            <div>
              <p className="text-[#A6A6A6] text-xs">Saldo Principal</p>
              <p className="text-[#E6E6E6] font-bold">
                ${user?.balance?.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) || '0.00'}
              </p>
            </div>
          </div>
          
          <div className="flex items-center space-x-2">
            <TrendingUp className="text-[#32FF7E]" size={20} />
            <div>
              <p className="text-[#A6A6A6] text-xs">Saldo Bot</p>
              <p className="text-[#E6E6E6] font-bold">
                ${user?.botBalance?.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) || '0.00'}
              </p>
            </div>
          </div>

          <div className="flex items-center space-x-2">
            <div className="w-2 h-2 bg-[#32FF7E] rounded-full animate-pulse"></div>
            <span className="text-[#32FF7E] text-sm">Sistema Online</span>
          </div>
        </div>

        <div className="flex items-center space-x-4">
          <button className="relative p-2 text-[#A6A6A6] hover:text-[#E6E6E6] transition-colors">
            <Bell size={20} />
            <span className="absolute -top-1 -right-1 w-4 h-4 bg-[#FF4D4D] text-white text-xs rounded-full flex items-center justify-center">
              3
            </span>
          </button>
          
          <div className="flex items-center space-x-2">
            <div className="w-8 h-8 bg-[#2188B6] rounded-full flex items-center justify-center">
              <span className="text-white text-sm font-bold">
                {user?.name?.charAt(0).toUpperCase()}
              </span>
            </div>
            <span className="text-[#E6E6E6]">{user?.name}</span>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;