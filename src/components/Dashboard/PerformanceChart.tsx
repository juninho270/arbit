import React from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

interface PerformanceData {
  date: string;
  profit: number;
  manual: number;
  bot: number;
}

interface PerformanceChartProps {
  data: PerformanceData[];
}

const PerformanceChart: React.FC<PerformanceChartProps> = ({ data }) => {
  return (
    <div className="bg-[#1A1A1A] rounded-lg border border-gray-800">
      <div className="p-6 border-b border-gray-800">
        <h3 className="text-[#E6E6E6] text-lg font-semibold">Performance (Últimos 30 Dias)</h3>
      </div>
      
      <div className="p-6">
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={data}>
            <CartesianGrid strokeDasharray="3 3" stroke="#444" />
            <XAxis 
              dataKey="date" 
              stroke="#A6A6A6"
              fontSize={12}
            />
            <YAxis 
              stroke="#A6A6A6"
              fontSize={12}
              tickFormatter={(value) => `$${value}`}
            />
            <Tooltip 
              contentStyle={{
                backgroundColor: '#1A1A1A',
                border: '1px solid #444',
                borderRadius: '8px',
                color: '#E6E6E6'
              }}
              formatter={(value: number, name: string) => [
                `$${value.toFixed(2)}`,
                name === 'profit' ? 'Total' : name === 'manual' ? 'Manual' : 'Bot'
              ]}
            />
            <Line 
              type="monotone" 
              dataKey="profit" 
              stroke="#32FF7E" 
              strokeWidth={2}
              dot={{ fill: '#32FF7E', strokeWidth: 2, r: 4 }}
            />
            <Line 
              type="monotone" 
              dataKey="manual" 
              stroke="#2188B6" 
              strokeWidth={2}
              dot={{ fill: '#2188B6', strokeWidth: 2, r: 4 }}
            />
            <Line 
              type="monotone" 
              dataKey="bot" 
              stroke="#FFD166" 
              strokeWidth={2}
              dot={{ fill: '#FFD166', strokeWidth: 2, r: 4 }}
            />
          </LineChart>
        </ResponsiveContainer>
        
        <div className="flex items-center justify-center space-x-6 mt-4">
          <div className="flex items-center space-x-2">
            <div className="w-3 h-3 bg-[#32FF7E] rounded-full"></div>
            <span className="text-[#A6A6A6] text-sm">Total</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-3 h-3 bg-[#2188B6] rounded-full"></div>
            <span className="text-[#A6A6A6] text-sm">Manual</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-3 h-3 bg-[#FFD166] rounded-full"></div>
            <span className="text-[#A6A6A6] text-sm">Bot</span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PerformanceChart;