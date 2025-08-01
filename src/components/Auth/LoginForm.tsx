import React, { useState } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { Lock, Mail, Eye, EyeOff, Loader2 } from 'lucide-react';

const LoginForm: React.FC = () => {
  const { login } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');

    try {
      const success = await login(email, password);
      
      if (!success) {
        setError('Credenciais inválidas');
      }
    } catch (error) {
      console.error('Login error:', error);
      setError('Erro ao fazer login. Tente novamente.');
    }
    
    setIsLoading(false);
  };

  return (
    <div className="min-h-screen bg-[#0D0D0D] flex items-center justify-center px-4">
      <div className="max-w-md w-full space-y-8">
        <div className="text-center">
          <h2 className="text-3xl font-bold text-[#E6E6E6]">CryptoArb Pro</h2>
          <p className="mt-2 text-[#A6A6A6]">Faça login em sua conta</p>
        </div>
        
        <form onSubmit={handleSubmit} className="bg-[#1A1A1A] p-8 rounded-lg border border-gray-800 space-y-6">
          <div>
            <label htmlFor="email" className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Email
            </label>
            <div className="relative">
              <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-[#A6A6A6]" size={18} />
              <input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full pl-10 pr-3 py-2 bg-[#0D0D0D] border border-gray-800 rounded-lg text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
                placeholder="seu@email.com"
              />
            </div>
          </div>

          <div>
            <label htmlFor="password" className="block text-[#A6A6A6] text-sm font-medium mb-2">
              Senha
            </label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-[#A6A6A6]" size={18} />
              <input
                id="password"
                type={showPassword ? 'text' : 'password'}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full pl-10 pr-10 py-2 bg-[#0D0D0D] border border-gray-800 rounded-lg text-[#E6E6E6] focus:border-[#2188B6] focus:outline-none"
                placeholder="Sua senha"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#A6A6A6] hover:text-[#E6E6E6]"
              >
                {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
              </button>
            </div>
          </div>

          {error && (
            <div className="text-[#FF4D4D] text-sm text-center">{error}</div>
          )}

          <button
            type="submit"
            disabled={isLoading}
            className="w-full flex items-center justify-center space-x-2 py-3 px-4 bg-[#2188B6] text-white rounded-lg font-medium hover:bg-[#2188B6]/90 focus:outline-none focus:ring-2 focus:ring-[#2188B6] focus:ring-offset-2 focus:ring-offset-[#1A1A1A] disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {isLoading ? (
              <Loader2 className="animate-spin" size={20} />
            ) : (
              <span>Entrar</span>
            )}
          </button>
        </form>

        <div className="text-center text-[#A6A6A6] text-sm">
          <p>Credenciais de teste:</p>
          <p>Email: admin@admin.com (Admin) ou user@user.com (Usuário)</p>
          <p>Senha: qualquer senha</p>
        </div>
      </div>
    </div>
  );
};

export default LoginForm;