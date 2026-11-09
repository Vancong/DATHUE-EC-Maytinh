import Chart from 'chart.js/auto';
window.Chart = Chart;

// Global Toast or Flash utility
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    const bgClass = type === 'success' ? 'bg-emerald-600 text-white' : (type === 'error' ? 'bg-rose-600 text-white' : 'bg-slate-800 text-white');
    toast.className = `flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition-all transform duration-300 translate-y-2 opacity-0 ${bgClass}`;
    toast.innerHTML = `<span>${message}</span>`;
    
    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
    });
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};
