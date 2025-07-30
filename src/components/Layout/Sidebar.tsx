import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import {
  LayoutDashboard,
  TrendingUp,
  Target,
  Bot,
  PiggyBank,
  Settings,
  Shield,
  LogOut,
  Menu,
  X,
  Bitcoin
} from 'lucide-react';

interface SidebarProps {
  isOpen: boolean;
  onToggle: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ isOpen, onToggle }) => {
  const { user, logout } = useAuth();
  const location = useLocation();

  // Different menu items based on user role
  const menuItems = user?.role === 'admin' 
    ? [
        { path: '/admin', icon: Shield, label: 'Painel Admin' },
      ]
    : [
        { path: '/', icon: LayoutDashboard, label: 'Dashboard' },
        { path: '/market', icon: TrendingUp, label: 'Mercado' },
        { path: '/arbitrage', icon: Target, label: 'Arbitragem' },
        { path: '/bot', icon: Bot, label: 'Bot Trading' },
        { path: '/investments', icon: PiggyBank, label: 'Investimentos' },
        { path: '/settings', icon: Settings, label: 'Configurações' },
      ];

  const handleLogout = () => {
    logout();
    onToggle(); // Close mobile menu on logout
  };

  return (
    <>
      {/* Mobile Overlay */}
      {isOpen && (
        <div 
          className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
          onClick={onToggle}
        />
      )}

      {/* Sidebar */}
      <div className={`
        fixed lg:static inset-y-0 left-0 z-50 w-64 bg-[#1A1A1A] border-r border-gray-800 
        transform transition-transform duration-300 ease-in-out lg:transform-none
        ${isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
      `}>
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-800">
          <div className="flex items-center space-x-3">
            <div className="w-8 h-8 bg-gradient-to-br from-[#2188B6] to-[#32FF7E] rounded-lg flex items-center justify-center">
              <Bitcoin className="w-5 h-5 text-white" />
            </div>
            <span className="text-[#E6E6E6] font-bold text-lg">CryptoArb</span>
          </div>
          
          {/* Mobile Close Button */}
          <button
            onClick={onToggle}
            className="lg:hidden text-[#A6A6A6] hover:text-[#E6E6E6] transition-colors"
          >
            <X size={24} />
          </button>
        </div>

        {/* User Info */}
        <div className="p-6 border-b border-gray-800">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-[#2188B6] rounded-full flex items-center justify-center">
              <span className="text-white font-semibold text-sm">
                {user?.name?.charAt(0).toUpperCase() || 'U'}
              </span>
            </div>
            <div>
              <p className="text-[#E6E6E6] font-medium text-sm">{user?.name}</p>
              <p className="text-[#A6A6A6] text-xs capitalize">{user?.role}</p>
            </div>
          </div>
          
          <div className="mt-4 p-3 bg-[#0D0D0D] rounded-lg">
            <p className="text-[#A6A6A6] text-xs">Saldo Total</p>
            <p className="text-[#32FF7E] font-bold text-lg">
              ${((user?.balance || 0) + (user?.botBalance || 0)).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
            </p>
          </div>
        </div>

        {/* Navigation */}
        <nav className="flex-1 p-4">
          <ul className="space-y-2">
            {menuItems.map((item) => {
              const Icon = item.icon;
              const isActive = location.pathname === item.path;
              
              return (
                <li key={item.path}>
                  <Link
                    to={item.path}
                    onClick={() => {
                      // Close mobile menu when navigating
                      if (window.innerWidth < 1024) {
                        onToggle();
                      }
                    }}
                    className={`
                      flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200
                      ${isActive 
                        ? 'bg-[#2188B6] text-white shadow-lg' 
                        : 'text-[#A6A6A6] hover:text-[#E6E6E6] hover:bg-[#0D0D0D]'
                      }
                    `}
                  >
                    <Icon size={20} />
                    <span className="font-medium">{item.label}</span>
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>

        {/* Logout Button */}
        <div className="p-4 border-t border-gray-800">
          <button
            onClick={handleLogout}
            className="flex items-center space-x-3 px-4 py-3 w-full text-[#A6A6A6] hover:text-[#FF4D4D] hover:bg-[#0D0D0D] rounded-lg transition-all duration-200"
          >
            <LogOut size={20} />
            <span className="font-medium">Sair</span>
          </button>
        </div>
      </div>
    </>
  );
};

export default Sidebar;