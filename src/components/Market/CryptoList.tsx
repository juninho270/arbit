import React, { useState, useEffect } from 'react';
import { Cryptocurrency } from '../../types';
import { getCryptocurrencies } from '../../utils/api';
import { Heart, TrendingUp, TrendingDown, Star, Loader2 } from 'lucide-react';

const CryptoList: React.FC = () => {
  const [cryptos, setCryptos] = useState<Cryptocurrency[]>([]);
  const [favorites, setFavorites] = useState<Set<string>>(new Set());
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState<'all' | 'favorites'>('all');

  useEffect(() => {
    loadCryptocurrencies();
    const interval = setInterval(loadCryptocurrencies, 300000); // Update every 5 minutes
    return () => clearInterval(interval);
  }, []);

  const loadCryptocurrencies = async () => {
    const data = await getCryptocurrencies(50);
    setCryptos(data);
    setLoading(false);
  };

  const toggleFavorite = (cryptoId: string) => {
    const newFavorites = new Set(favorites);
    if (newFavorites.has(cryptoId)) {
      newFavorites.delete(cryptoId);
    } else {
      newFavorites.add(cryptoId);
    }
    setFavorites(newFavorites);
    localStorage.setItem('favorites', JSON.stringify(Array.from(newFavorites)));
  };

  const filteredCryptos = filter === 'favorites' 
    ? cryptos.filter(crypto => favorites.has(crypto.id))
    : cryptos;

  if (loading) {
    return (
      <div className="bg-[#1A1A1A] rounded-lg border border-gray-800 p-8">
        <div className="flex items-center justify-center">
          <Loader2 className="animate-spin text-[#2188B6]" size={32} />
          <span className="ml-2 text-[#E6E6E6]">Carregando preços...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-[#1A1A1A] rounded-lg border border-gray-800">
      <div className="p-6 border-b border-gray-800">
        <div className="flex items-center justify-between">
          <h3 className="text-[#E6E6E6] text-lg font-semibold">Mercado de Criptomoedas</h3>
          <div className="flex space-x-2">
            <button
              onClick={() => setFilter('all')}
              className={`px-4 py-2 rounded-lg transition-colors ${
                filter === 'all'
                  ? 'bg-[#2188B6] text-white'
                  : 'bg-[#0D0D0D] text-[#A6A6A6] hover:text-[#E6E6E6]'
              }`}
            >
              Todas
            </button>
            <button
              onClick={() => setFilter('favorites')}
              className={`px-4 py-2 rounded-lg transition-colors flex items-center space-x-2 ${
                filter === 'favorites'
                  ? 'bg-[#2188B6] text-white'
                  : 'bg-[#0D0D0D] text-[#A6A6A6] hover:text-[#E6E6E6]'
              }`}
            >
              <Star size={16} />
              <span>Favoritas</span>
            </button>
          </div>
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full">
          <thead className="bg-[#0D0D0D]">
            <tr>
              <th className="text-left p-4 text-[#A6A6A6] font-medium">Moeda</th>
              <th className="text-right p-4 text-[#A6A6A6] font-medium">Preço</th>
              <th className="text-right p-4 text-[#A6A6A6] font-medium">24h</th>
              <th className="text-right p-4 text-[#A6A6A6] font-medium">Market Cap</th>
              <th className="text-right p-4 text-[#A6A6A6] font-medium">Volume 24h</th>
              <th className="text-center p-4 text-[#A6A6A6] font-medium">Favorito</th>
            </tr>
          </thead>
          <tbody>
            {filteredCryptos.map((crypto) => {
              const isPositive = crypto.price_change_percentage_24h > 0;
              return (
                <tr key={crypto.id} className="border-b border-gray-800 hover:bg-[#0D0D0D] transition-colors">
                  <td className="p-4">
                    <div className="flex items-center space-x-3">
                      <img 
                        src={crypto.image} 
                        alt={crypto.name} 
                        className="w-8 h-8 rounded-full"
                      />
                      <div>
                        <p className="text-[#E6E6E6] font-medium">{crypto.name}</p>
                        <p className="text-[#A6A6A6] text-sm">{crypto.symbol.toUpperCase()}</p>
                      </div>
                    </div>
                  </td>
                  <td className="p-4 text-right">
                    <span className="text-[#E6E6E6] font-mono">
                      ${crypto.current_price.toLocaleString('en-US', { 
                        minimumFractionDigits: 2,
                        maximumFractionDigits: crypto.current_price < 1 ? 6 : 2
                      })}
                    </span>
                  </td>
                  <td className="p-4 text-right">
                    <div className={`flex items-center justify-end space-x-1 ${
                      isPositive ? 'text-[#32FF7E]' : 'text-[#FF4D4D]'
                    }`}>
                      {isPositive ? <TrendingUp size={16} /> : <TrendingDown size={16} />}
                      <span className="font-mono">
                        {crypto.price_change_percentage_24h.toFixed(2)}%
                      </span>
                    </div>
                  </td>
                  <td className="p-4 text-right">
                    <span className="text-[#A6A6A6] font-mono">
                      ${(crypto.market_cap || 0).toLocaleString()}
                    </span>
                  </td>
                  <td className="p-4 text-right">
                    <span className="text-[#A6A6A6] font-mono">
                      ${(crypto.volume_24h || 0).toLocaleString()}
                    </span>
                  </td>
                  <td className="p-4 text-center">
                    <button
                      onClick={() => toggleFavorite(crypto.id)}
                      className="p-1 hover:bg-[#2188B6]/20 rounded transition-colors"
                    >
                      <Heart 
                        size={18} 
                        className={favorites.has(crypto.id) 
                          ? 'text-[#FF4D4D] fill-current' 
                          : 'text-[#A6A6A6]'
                        }
                      />
                    </button>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default CryptoList;