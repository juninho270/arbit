export interface User {
  id: string;
  email: string;
  name: string;
  balance: number;
  botBalance: number;
  status: 'active' | 'suspended' | 'pending';
  createdAt: string;
  role: 'user' | 'admin';
  lastLogin?: string;
}

export interface Cryptocurrency {
  id: string;
  symbol: string;
  name: string;
  current_price: number;
  price_change_percentage_24h: number;
  market_cap: number;
  volume_24h: number;
  image: string;
  isFavorite?: boolean;
  contractAddress?: string;
  isArbitrageEnabled?: boolean;
  deactivationReason?: string;
  sparkline_in_7d?: {
    price: number[];
  };
}

export interface ArbitrageOperation {
  id: string;
  userId: string;
  type: 'manual' | 'bot';
  cryptocurrency: string;
  amount: number;
  buyPrice: number;
  sellPrice: number;
  profit: number;
  profitPercentage: number;
  status: 'pending' | 'completed' | 'failed';
  status: 'pending' | 'completed' | 'failed' | 'cancelled_no_hash';
  transactionHash?: string;
  chain?: string;
  noHashReason?: string;
  executionTime: number;
  createdAt: string;
  completedAt?: string;
  errorMessage?: string;
}

export interface BotSettings {
  id: string;
  userId: string;
  isActive: boolean;
  minProfit: number;
  maxAmount: number;
  interval: number;
  selectedCoins: string[];
  autoReinvest: boolean;
  stopLoss: number;
  createdAt: string;
  updatedAt: string;
}

export interface Investment {
  id: string;
  userId: string;
  planId: string;
  amount: number;
  expectedReturn: number;
  currentReturn: number;
  duration: number;
  status: 'active' | 'completed' | 'cancelled';
  startDate: string;
  endDate: string;
  progress: number;
}

export interface InvestmentPlan {
  id: string;
  name: string;
  description: string;
  minAmount: number;
  maxAmount: number;
  returnPercentage: number;
  duration: number; // in days
  isActive: boolean;
  risk: 'low' | 'medium' | 'high';
}

export interface AdminStats {
  totalUsers: number;
  totalOperations: number;
  totalProfit: number;
  activeInvestments: number;
  activeBots: number;
  dailyVolume: number;
}