import React from 'react';
import { DivideIcon as LucideIcon } from 'lucide-react';

interface StatsCardProps {
  title: string;
  value: string;
  change?: string;
  changeType?: 'positive' | 'negative' | 'neutral';
  icon: LucideIcon;
  description?: string;
}

const StatsCard: React.FC<StatsCardProps> = ({
  title,
  value,
  change,
  changeType = 'neutral',
  icon: Icon,
  description
}) => {
  const getChangeColor = () => {
    switch (changeType) {
      case 'positive':
        return 'text-[#32FF7E]';
      case 'negative':
        return 'text-[#FF4D4D]';
      default:
        return 'text-[#A6A6A6]';
    }
  };

  return (
    <div className="bg-[#1A1A1A] p-6 rounded-lg border border-gray-800 hover:border-[#2188B6] transition-colors">
      <div className="flex items-center justify-between mb-4">
        <Icon className="text-[#2188B6]" size={24} />
        {change && (
          <span className={`text-sm font-medium ${getChangeColor()}`}>
            {changeType === 'positive' ? '+' : changeType === 'negative' ? '-' : ''}{change}
          </span>
        )}
      </div>
      
      <div>
        <h3 className="text-[#A6A6A6] text-sm font-medium mb-1">{title}</h3>
        <p className="text-[#E6E6E6] text-2xl font-bold mb-1">{value}</p>
        {description && (
          <p className="text-[#A6A6A6] text-xs">{description}</p>
        )}
      </div>
    </div>
  );
};

export default StatsCard;