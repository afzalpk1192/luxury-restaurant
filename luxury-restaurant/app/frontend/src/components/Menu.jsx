import React, { useState, useEffect } from 'react';
import { menuCategories, menuItems as defaultMenuItems } from '../data/restaurantData';
import { fetchMenuItems } from '../api/restaurantApi';

export default function Menu({ onAddToCart }) {
  const [activeCategory, setActiveCategory] = useState("All");
  const [items, setItems] = useState(() => {
    return defaultMenuItems.map(item => ({
      ...item,
      id: item._id || item.id
    }));
  });

  useEffect(() => {
    fetchMenuItems()
      .then(data => {
        if (data && data.length > 0) {
          const normalized = data.map(item => ({
            ...item,
            id: item._id || item.id,
            dietary: Array.isArray(item.dietary) ? item.dietary : []
          }));
          setItems(normalized);
        }
      })
      .catch(err => {
        console.warn('Using local fallback menu items:', err.message);
      });
  }, []);

  const filteredItems = activeCategory === "All" 
    ? items 
    : items.filter(item => item.category === activeCategory);

  return (
    <section id="menu" className="py-16 sm:py-24 bg-gradient-to-b from-[#080b12] to-[#0b0f19] scroll-mt-20 sm:scroll-mt-24">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        <div className="text-center mb-10 sm:mb-16">
          <p className="text-amber-400 text-xs uppercase tracking-[0.25em] mb-2 font-medium">Exquisite Culinary Offerings</p>
          <h2 className="text-3xl sm:text-4xl md:text-5xl font-serif text-white mb-2 break-words">À La Carte Menu</h2>
          <p className="text-gray-400 text-xs sm:text-sm max-w-2xl mx-auto px-2">Meticulously crafted dishes featuring the finest seasonal ingredients</p>
        </div>

        {/* Category Tabs */}
        <div className="flex flex-wrap justify-center gap-1.5 sm:gap-3 mb-10 sm:mb-16 max-w-full">
          {menuCategories.map((cat) => (
            <button
              key={cat}
              onClick={() => setActiveCategory(cat)}
              className={`px-3 sm:px-6 py-1.5 sm:py-2.5 text-[10px] sm:text-xs uppercase tracking-wider sm:tracking-widest font-semibold transition-all duration-300 rounded ${
                activeCategory === cat 
                  ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-black shadow-lg shadow-amber-500/50' 
                  : 'text-gray-400 border border-gray-700 hover:border-amber-500/50 hover:text-amber-300'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>

        {/* Menu Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
          {filteredItems.map((item) => {
            const itemKey = item._id || item.id || item.name;
            const dietaryList = Array.isArray(item.dietary) ? item.dietary : [];

            return (
              <div key={itemKey} className="group bg-gradient-to-br from-[#111625] to-[#0b0f19] border border-gray-800/60 p-4 sm:p-6 rounded-lg flex flex-col sm:flex-row gap-4 sm:gap-6 hover:border-amber-500/50 hover:shadow-2xl hover:shadow-amber-500/20 transition-all duration-500">
                <div className="relative overflow-hidden rounded-lg w-full sm:w-40 h-44 sm:h-40 flex-shrink-0">
                  <img src={item.image} alt={item.name} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div className="flex-1 flex flex-col justify-between">
                  <div>
                    <div className="flex justify-between items-start mb-2 sm:mb-3 gap-2 sm:gap-4">
                      <h3 className="text-base sm:text-lg font-serif text-white group-hover:text-amber-300 transition-colors duration-300 flex-1 leading-snug break-words">{item.name}</h3>
                      <span className="text-amber-400 font-serif font-bold text-lg sm:text-xl flex-shrink-0">${item.price}</span>
                    </div>
                    <p className="text-gray-400 text-xs sm:text-sm leading-relaxed mb-4">{item.description}</p>
                  </div>
                  <div className="flex flex-wrap sm:flex-nowrap justify-between items-center gap-3 pt-3 border-t border-gray-800/50 mt-auto">
                    <div className="flex gap-1.5 flex-wrap">
                      {dietaryList.map(d => (
                        <span key={d} className="text-[10px] sm:text-[11px] text-amber-300/80 border border-amber-500/40 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded bg-amber-500/5">{d}</span>
                      ))}
                    </div>
                    <button 
                      onClick={() => onAddToCart(item)}
                      className="ml-auto sm:ml-0 text-xs uppercase tracking-wider bg-amber-500/10 sm:bg-transparent hover:bg-amber-500 sm:hover:bg-transparent text-amber-400 hover:text-black sm:hover:text-amber-300 px-3 py-1.5 sm:px-0 sm:py-0 rounded sm:rounded-none border border-amber-500/30 sm:border-0 font-semibold flex items-center gap-1.5 sm:gap-2 transition-all duration-300 group/btn hover:gap-2.5 cursor-pointer"
                    >
                      <span>+ Add</span>
                      <span className="group-hover/btn:translate-x-1 transition-transform duration-300">→</span>
                    </button>
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        {/* Empty state fallback */}
        {filteredItems.length === 0 && (
          <div className="text-center py-12 px-4 bg-[#111625]/50 border border-amber-500/20 rounded-lg max-w-md mx-auto">
            <p className="text-gray-300 text-sm mb-4">No dishes found in this category.</p>
            <button
              onClick={() => setActiveCategory("All")}
              className="px-4 py-2 bg-amber-500 text-black text-xs uppercase tracking-wider font-bold rounded hover:bg-amber-400 transition-colors cursor-pointer"
            >
              Show All Dishes
            </button>
          </div>
        )}
      </div>
    </section>
  );
}