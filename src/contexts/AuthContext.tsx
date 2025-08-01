import React, { createContext, useContext, useState, useEffect } from 'react';
import { User } from '../types';
import { authAPI, usersAPI } from '../utils/api';

interface AuthContextType {
  user: User | null;
  login: (email: string, password: string) => Promise<boolean>;
  logout: () => void;
  isLoading: boolean;
  updateBalance: (newBalance: number) => void;
  updateBotBalance: (newBalance: number) => void;
  updateUser: (userId: string, updatedData: Partial<User>) => Promise<boolean>;
  loginAsUser: (targetUser: User) => void;
  getAllUsers: () => User[];
  addUser: (userData: Omit<User, 'id' | 'createdAt'>) => Promise<boolean>;
  deleteUser: (userId: string) => Promise<boolean>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [allUsers, setAllUsers] = useState<User[]>([]);

  useEffect(() => {
    checkAuthStatus();
  }, []);

  const checkAuthStatus = async () => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      try {
        const userData = await authAPI.me();
        setUser(userData);
        
        // Load all users if admin
        if (userData.role === 'admin') {
          const users = await usersAPI.getAll();
          setAllUsers(users);
        }
      } catch (error) {
        console.log('Token validation failed, logging out...');
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        setUser(null);
        setAllUsers([]);
      }
    }
    setIsLoading(false);
  };

  const login = async (email: string, password: string): Promise<boolean> => {
    setIsLoading(true);
    try {
      const response = await authAPI.login(email, password);
      const { user: userData, token } = response;
      
      localStorage.setItem('auth_token', token);
      localStorage.setItem('user', JSON.stringify(userData));
      setUser(userData);
      
      // Load all users if admin
      if (userData.role === 'admin') {
        const users = await usersAPI.getAll();
        setAllUsers(users);
      }
      
      setIsLoading(false);
      return true;
    } catch (error) {
      setIsLoading(false);
      return false;
    }
  };

  const logout = () => {
    authAPI.logout().catch(() => {}); // Don't wait for response
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    setUser(null);
    setAllUsers([]);
  };

  const updateBalance = (newBalance: number) => {
    if (user) {
      const updatedUser = { ...user, balance: newBalance };
      setUser(updatedUser);
      localStorage.setItem('user', JSON.stringify(updatedUser));
      
      // Update via API
      usersAPI.updateBalance(user.id, newBalance, user.botBalance).catch(console.error);
    }
  };

  const updateBotBalance = (newBalance: number) => {
    if (user) {
      const updatedUser = { ...user, botBalance: newBalance };
      setUser(updatedUser);
      localStorage.setItem('user', JSON.stringify(updatedUser));
      
      // Update via API
      usersAPI.updateBalance(user.id, user.balance, newBalance).catch(console.error);
    }
  };

  const updateUser = async (userId: string, updatedData: Partial<User>): Promise<boolean> => {
    try {
      const updatedUser = await usersAPI.update(userId, updatedData);
      
      // Update local state
      setAllUsers(prev => prev.map(u => u.id === userId ? updatedUser : u));
      
      // If updating current user, update the user state as well
      if (user && user.id.toString() === userId) {
        setUser(updatedUser);
        localStorage.setItem('user', JSON.stringify(updatedUser));
      }
      
      return true;
    } catch (error) {
      console.error('Error updating user:', error);
      return false;
    }
  };

  const loginAsUser = (targetUser: User) => {
    authAPI.loginAsUser(targetUser.id.toString())
      .then(response => {
        const { user: userData, token } = response;
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user', JSON.stringify(userData));
        setUser(userData);
      })
      .catch(console.error);
  };

  const getAllUsers = (): User[] => {
    return allUsers;
  };

  const addUser = async (userData: Omit<User, 'id' | 'createdAt'>): Promise<boolean> => {
    try {
      const newUser = await usersAPI.create(userData);
      
      setAllUsers(prev => [...prev, newUser]);
      
      return true;
    } catch (error) {
      console.error('Error adding user:', error);
      return false;
    }
  };

  const deleteUser = async (userId: string): Promise<boolean> => {
    try {
      await usersAPI.delete(userId);
      
      setAllUsers(prev => prev.filter(u => u.id.toString() !== userId));
      
      return true;
    } catch (error) {
      console.error('Error deleting user:', error);
      return false;
    }
  };

  return (
    <AuthContext.Provider value={{
      user,
      login,
      logout,
      isLoading,
      updateBalance,
      updateBotBalance,
      updateUser,
      loginAsUser,
      getAllUsers,
      addUser,
      deleteUser
    }}>
      {children}
    </AuthContext.Provider>
  );
};