import axios from 'axios';
import { Cryptocurrency, ArbitrageOperation, User, BotSettings, Investment, InvestmentPlan } from '../types';

// Laravel API Configuration
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add auth token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Let components handle 401 errors themselves
    // Only clear storage if it's not a login attempt
    if (error.response?.status === 401 && !error.config?.url?.includes('/login')) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
    }
    return Promise.reject(error);
  }
);

// Auth API
export const authAPI = {
  login: async (email: string, password: string) => {
    const response = await api.post('/login', { email, password });
    return response.data;
  },

  register: async (name: string, email: string, password: string) => {
    const response = await api.post('/register', { name, email, password });
    return response.data;
  },

  logout: async () => {
    await api.post('/logout');
  },

  me: async () => {
    const response = await api.get('/me');
    return response.data;
  },

  loginAsUser: async (userId: string) => {
    const response = await api.post('/login-as-user', { user_id: userId });
    return response.data;
  },
};

// Users API
export const usersAPI = {
  getAll: async (): Promise<User[]> => {
    const response = await api.get('/users');
    return response.data;
  },

  create: async (userData: Omit<User, 'id' | 'createdAt'>): Promise<User> => {
    const response = await api.post('/users', userData);
    return response.data;
  },

  update: async (userId: string, userData: Partial<User>): Promise<User> => {
    const response = await api.patch(`/users/${userId}`, userData);
    return response.data;
  },

  delete: async (userId: string): Promise<void> => {
    await api.delete(`/users/${userId}`);
  },

  updateBalance: async (userId: string, balance: number, botBalance: number): Promise<User> => {
    const response = await api.patch(`/users/${userId}/balance`, { balance, bot_balance: botBalance });
    return response.data;
  },
};

export const getCryptocurrencies = async (limit: number = 50): Promise<Cryptocurrency[]> => {
  try {
    const response = await api.get('/cryptocurrencies', { params: { limit } });
    return response.data;
  } catch (error) {
    console.error('Error fetching cryptocurrencies:', error);
    return [];
  }
};

export const updateCryptoArbitrageStatus = async (cryptoId: string, enabled: boolean, reason?: string): Promise<void> => {
  try {
    await api.patch(`/cryptocurrencies/${cryptoId}/arbitrage-status`, { enabled, reason });
  } catch (error) {
    console.error('Error updating crypto arbitrage status:', error);
  }
};

export const getCryptocurrencyPrice = async (coinId: string): Promise<number> => {
  try {
    const response = await api.get(`/cryptocurrencies/${coinId}/price`);
    return response.data.price;
  } catch (error) {
    console.error('Error fetching price:', error);
    return 0;
  }
};


// Arbitrage API
export const arbitrageAPI = {
  getOperations: async (): Promise<ArbitrageOperation[]> => {
    const response = await api.get('/arbitrage/operations');
    return response.data.data || response.data;
  },

  executeManual: async (cryptocurrency: string, amount: number, coinId: string): Promise<ArbitrageOperation> => {
    const response = await api.post('/arbitrage/execute-manual', {
      cryptocurrency,
      amount,
      coin_id: coinId,
    });
    return response.data;
  },

  getRecent: async (): Promise<ArbitrageOperation[]> => {
    const response = await api.get('/arbitrage/recent');
    return response.data;
  },
};

// Bot API
export const botAPI = {
  getSettings: async (): Promise<BotSettings> => {
    const response = await api.get('/bot/settings');
    return response.data;
  },

  updateSettings: async (settings: Partial<BotSettings>): Promise<BotSettings> => {
    const response = await api.patch('/bot/settings', settings);
    return response.data;
  },

  getStatistics: async () => {
    const response = await api.get('/bot/statistics');
    return response.data;
  },
};

// Investment API
export const investmentAPI = {
  getPlans: async (): Promise<InvestmentPlan[]> => {
    const response = await api.get('/investments/plans');
    return response.data;
  },

  getUserInvestments: async (): Promise<Investment[]> => {
    const response = await api.get('/investments/user');
    return response.data;
  },

  createInvestment: async (planId: string, amount: number): Promise<Investment> => {
    const response = await api.post('/investments', { plan_id: planId, amount });
    return response.data;
  },

  getStatistics: async () => {
    const response = await api.get('/investments/statistics');
    return response.data;
  },
};

// Admin API
export const adminAPI = {
  getStats: async () => {
    const response = await api.get('/admin/stats');
    return response.data;
  },

  getSystemSettings: async () => {
    const response = await api.get('/admin/system-settings');
    return response.data;
  },

  updateSystemSettings: async (settings: any) => {
    const response = await api.patch('/admin/system-settings', settings);
    return response.data;
  },

  getRecentActivity: async () => {
    const response = await api.get('/admin/recent-activity');
    return response.data;
  },
};

export const findTransactionHash = async (coinId: string, amount: number): Promise<{ hash: string; chain: string } | null> => {
  // Simulate finding a real transaction hash
  // In a real implementation, this would search across multiple blockchain explorers
  
  // Simulate some delay
  await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));
  
  // Simulate success/failure (70% success rate)
  if (Math.random() < 0.7) {
    const chains = ['bsc', 'eth', 'polygon', 'avalanche', 'arbitrum'];
    const selectedChain = chains[Math.floor(Math.random() * chains.length)];
    
    // Generate a realistic-looking transaction hash
    const hash = '0x' + Array.from({length: 64}, () => 
      Math.floor(Math.random() * 16).toString(16)
    ).join('');
    
    return { hash, chain: selectedChain };
  }
  
  // Return null to simulate no transaction found
  return null;
};

export const simulateArbitrageProfit = (amount: number): number => {
  // Simulate profit between 2% and 8% of the investment
  const profitPercentage = 0.02 + Math.random() * 0.06;
  return amount * profitPercentage;
};

export const simulateExecutionTime = (): number => {
  // Simulate execution time between 3-8 seconds
  return 3000 + Math.random() * 5000;
};

export const getExplorerUrl = (hash: string, chain: string): string => {
  const explorers: { [key: string]: string } = {
    'bsc': 'https://bscscan.com/tx/',
    'eth': 'https://etherscan.io/tx/',
    'polygon': 'https://polygonscan.com/tx/',
    'avalanche': 'https://snowtrace.io/tx/',
    'arbitrum': 'https://arbiscan.io/tx/',
    'optimism': 'https://optimistic.etherscan.io/tx/',
    'cronos': 'https://cronoscan.com/tx/',
    'fantom': 'https://ftmscan.com/tx/',
    'palm': 'https://explorer.palm.io/tx/',
    'base': 'https://basescan.org/tx/',
    'moonbeam': 'https://moonscan.io/tx/',
    'moonriver': 'https://moonriver.moonscan.io/tx/',
    'harmony': 'https://explorer.harmony.one/tx/'
  };
  
  const baseUrl = explorers[chain] || 'https://bscscan.com/tx/';
  return `${baseUrl}${hash}`;
};
