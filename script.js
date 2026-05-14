// ==================== DATA MODELS ====================
let products = JSON.parse(localStorage.getItem('products')) || [];
let suppliers = JSON.parse(localStorage.getItem('suppliers')) || [];
let expenses = JSON.parse(localStorage.getItem('expenses')) || [];
let incomes = JSON.parse(localStorage.getItem('incomes')) || [];

// ==================== HELPER FUNCTIONS ====================
function saveData() {
    localStorage.setItem('products', JSON.stringify(products));
    localStorage.setItem('suppliers', JSON.stringify(suppliers));
    localStorage.setItem('expenses', JSON.stringify(expenses));
    localStorage.setItem('incomes', JSON.stringify(incomes));
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount);
}

function showNotification(title, message) {
    if (Notification.permission === 'granted' && document.getElementById('notificationsSwitch')?.checked) {
        new Notification(title, { body: message, icon: 'pazar.jpg' });
    }
}

// Check low stock
function checkLowStock() {
    const lowStockProducts = products.filter(p => p.stock < 5);
    if (lowStockProducts.length > 0) {
        showNotification('Düşük Stok Uyarısı', `${lowStockProducts.length} ürünün stoğu 5'in altında!`);
    }
}

// ==================== PAGE RENDERING ====================
function renderDashboard() {
    const totalIncome = incomes.reduce((sum, inc) => sum + inc.amount, 0);
    const totalExpense = expenses.reduce((sum, exp) => sum + exp.amount, 0);
    const netProfit = totalIncome - totalExpense;
    
    document.getElementById('totalIncome').innerHTML = formatCurrency(totalIncome);
    document.getElementById('totalExpense').innerHTML = formatCurrency(totalExpense);
    document.getElementById('netProfit').innerHTML = formatCurrency(netProfit);
    
    // Top selling product
    const productSales = {};
    incomes.forEach(inc => {
        if (inc.productSales) {
            inc.productSales.forEach(sale => {
                productSales[sale.name] = (productSales[sale.name] || 0) + sale.quantity;
            });
        }
    });
    const topProduct = Object.entries(productSales).sort((a,b) => b[1] - a[1])[0];
    document.getElementById('topProduct').innerText = topProduct ? `${topProduct[0]} (${topProduct[1]} adet)` : '-';
    
    // Most profitable
    const productProfits = products.map(p => ({ name: p.name, profit: (p.price - p.cost) * (p.stockSold || 0) }));
    const mostProfitable = productProfits.sort((a,b) => b.profit - a.profit)[0];
    document.getElementById('mostProfitable').innerText = mostProfitable ? `${mostProfitable.name} (${formatCurrency(mostProfitable.profit)})` : '-';
    
    // Low stock list
    const lowStock = products.filter(p => p.stock < 5);
    const lowStockList = document.getElementById('lowStockList');
    lowStockList.innerHTML = lowStock.map(p => `<li>${p.name}: ${p.stock} ${p.unit}</li>`).join('');
    if (lowStock.length === 0) lowStockList.innerHTML = '<li>Tüm ürünler yeterli stokta</li>';
    
    // Chart
    const last7Days = [...Array(7)].map((_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - i);
        return d.toISOString().split('T')[0];
    }).reverse();
    const salesData = last7Days.map(date => {
        return incomes.filter(inc => inc.date === date).reduce((sum, inc) => sum + inc.amount, 0);
    });
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    if (window.salesChart) window.salesChart.destroy();
    window.salesChart = new Chart(ctx, {
        type: 'line',
        data: { labels: last7Days, datasets: [{ label: 'Günlük Satış (₺)', data: salesData, borderColor: '#F57C00', tension: 0.3 }] }
    });
}

function renderProducts() {
    const search = document.getElementById('productSearch')?.value.toLowerCase() || '';
    const category = document.getElementById('categoryFilter')?.value || '';
    let filtered = products.filter(p => p.name.toLowerCase().includes(search));
    if (category) filtered = filtered.filter(p => p.category === category);
    
    const container = document.getElementById('productsList');
    container.innerHTML = filtered.map(p => `
        <div class="product-card">
            ${p.image ? `<img src="${p.image}" alt="${p.name}">` : '<i class="fas fa-image" style="font-size:50px; text-align:center; display:block;"></i>'}
            <h4>${p.name}</h4>
            <p>Kategori: ${p.category}</p>
            <p>Fiyat: ${formatCurrency(p.price)}/${p.unit}</p>
            <p>Stok: ${p.stock} ${p.unit}</p>
            <div class="product-actions">
                <button onclick="editProduct('${p.id}')" class="btn-secondary"><i class="fas fa-edit"></i></button>
                <button onclick="deleteProduct('${p.id}')" class="btn-danger"><i class="fas fa-trash"></i></button>
                <button onclick="updateStock('${p.id}')" class="btn-primary"><i class="fas fa-boxes"></i></button>
            </div>
        </div>
    `).join('');
}

function renderSuppliers() {
    const container = document.getElementById('suppliersList');
    container.innerHTML = suppliers.map(s => `
        <div class="supplier-card">
            <h4><i class="fas fa-user-tie"></i> ${s.name}</h4>
            <p>Ürün: ${s.product}</p>
            <p>Alış Tarihi: ${s.date}</p>
            <p>Miktar: ${s.quantity}</p>
            <p>Toplam Maliyet: ${formatCurrency(s.totalCost)}</p>
            <button onclick="deleteSupplier('${s.id}')" class="btn-danger">Sil</button>
        </div>
    `).join('');
}

function renderExpenses() {
    const container = document.getElementById('expensesList');
    container.innerHTML = expenses.map(e => `
        <div class="expense-card">
            <h4><i class="fas fa-tag"></i> ${e.type}</h4>
            <p>Açıklama: ${e.description}</p>
            <p>Tarih: ${e.date}</p>
            <p>Tutar: ${formatCurrency(e.amount)}</p>
            <button onclick="deleteExpense('${e.id}')" class="btn-danger">Sil</button>
        </div>
    `).join('');
}

function renderIncomes() {
    const container = document.getElementById('incomesList');
    container.innerHTML = incomes.map(i => `
        <div class="income-card">
            <h4><i class="fas fa-money-bill-wave"></i> Gelir</h4>
            <p>Açıklama: ${i.description}</p>
            <p>Tarih: ${i.date}</p>
            <p>Tutar: ${formatCurrency(i.amount)}</p>
            <button onclick="deleteIncome('${i.id}')" class="btn-danger">Sil</button>
        </div>
    `).join('');
}

function renderReports() {
    const today = new Date().toISOString().split('T')[0];
    const weekAgo = new Date(Date.now() - 7*86400000).toISOString().split('T')[0];
    const monthAgo = new Date(Date.now() - 30*86400000).toISOString().split('T')[0];
    
    const dailyIncomes = incomes.filter(i => i.date === today).reduce((s,i) => s+i.amount,0);
    const dailyExpenses = expenses.filter(e => e.date === today).reduce((s,e) => s+e.amount,0);
    const weeklyIncomes = incomes.filter(i => i.date >= weekAgo).reduce((s,i) => s+i.amount,0);
    const weeklyExpenses = expenses.filter(e => e.date >= weekAgo).reduce((s,e) => s+e.amount,0);
    const monthlyIncomes = incomes.filter(i => i.date >= monthAgo).reduce((s,i) => s+i.amount,0);
    const monthlyExpenses = expenses.filter(e => e.date >= monthAgo).reduce((s,e) => s+e.amount,0);
    
    document.getElementById('dailyProfitLoss').innerHTML = formatCurrency(dailyIncomes - dailyExpenses);
    document.getElementById('weeklyProfitLoss').innerHTML = formatCurrency(weeklyIncomes - weeklyExpenses);
    document.getElementById('monthlyProfitLoss').innerHTML = formatCurrency(monthlyIncomes - monthlyExpenses);
    
    // Weekly chart
    const days = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
    const weeklySales = days.map((_, idx) => {
        const d = new Date();
        d.setDate(d.getDate() - (6 - idx));
        const dateStr = d.toISOString().split('T')[0];
        return incomes.filter(i => i.date === dateStr).reduce((s,i) => s+i.amount,0);
    });
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    if (window.weeklyChart) window.weeklyChart.destroy();
    window.weeklyChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: days, datasets: [{ label: 'Günlük Gelir (₺)', data: weeklySales, backgroundColor: '#2E7D32' }] }
    });
}

// ==================== CRUD OPERATIONS ====================
function addProduct(product) {
    product.id = Date.now().toString();
    product.stockSold = 0;
    products.push(product);
    saveData();
    renderProducts();
    renderDashboard();
    checkLowStock();
}

function editProduct(id) {
    const product = products.find(p => p.id === id);
    if (product) {
        document.getElementById('productModalTitle').innerText = 'Ürün Düzenle';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category;
        document.getElementById('productUnit').value = product.unit;
        document.getElementById('productCost').value = product.cost;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productModal').style.display = 'flex';
    }
}

function deleteProduct(id) {
    if (confirm('Ürünü silmek istediğinize emin misiniz?')) {
        products = products.filter(p => p.id !== id);
        saveData();
        renderProducts();
        renderDashboard();
    }
}

function updateStock(id) {
    const newStock = prompt('Yeni stok miktarını girin:');
    if (newStock !== null) {
        const product = products.find(p => p.id === id);
        if (product) {
            product.stock = parseInt(newStock);
            saveData();
            renderProducts();
            checkLowStock();
        }
    }
}

// Similar functions for suppliers, expenses, incomes
function addSupplier(supplier) { supplier.id = Date.now().toString(); suppliers.push(supplier); saveData(); renderSuppliers(); }
function deleteSupplier(id) { suppliers = suppliers.filter(s => s.id !== id); saveData(); renderSuppliers(); }
function addExpense(expense) { expense.id = Date.now().toString(); expenses.push(expense); saveData(); renderExpenses(); renderDashboard(); }
function deleteExpense(id) { expenses = expenses.filter(e => e.id !== id); saveData(); renderExpenses(); renderDashboard(); }
function addIncome(income) { income.id = Date.now().toString(); incomes.push(income); saveData(); renderIncomes(); renderDashboard(); }
function deleteIncome(id) { incomes = incomes.filter(i => i.id !== id); saveData(); renderIncomes(); renderDashboard(); }

// ==================== EXPORT FUNCTIONS ====================
function exportToExcel() {
    const data = [...products, ...suppliers, ...expenses, ...incomes];
    console.log('Excel export simulated', data);
    alert('Excel export özelliği için kütüphane eklenebilir (xlsx).');
}

function exportToPDF() {
    alert('PDF export özelliği için jsPDF eklenebilir.');
}

// ==================== UI INITIALIZATION ====================
function initEventListeners() {
    // Page navigation
    document.querySelectorAll('.nav-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const pageId = btn.getAttribute('data-page');
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            document.getElementById(`${pageId}Page`).classList.add('active');
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            btn.classList.add('active');
            
            if (pageId === 'dashboard') renderDashboard();
            if (pageId === 'products') renderProducts();
            if (pageId === 'suppliers') renderSuppliers();
            if (pageId === 'expenses') renderExpenses();
            if (pageId === 'incomes') renderIncomes();
            if (pageId === 'reports') renderReports();
        });
    });
    
    // Dark mode
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    darkModeToggle.addEventListener('click', () => {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        document.body.setAttribute('data-theme', isDark ? 'light' : 'dark');
        darkModeSwitch.checked = !isDark;
        localStorage.setItem('darkMode', !isDark);
    });
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.setAttribute('data-theme', 'dark');
        darkModeSwitch.checked = true;
    }
    
    // Modals
    document.getElementById('addProductBtn').onclick = () => {
        document.getElementById('productModalTitle').innerText = 'Ürün Ekle';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productModal').style.display = 'flex';
    };
    document.querySelectorAll('.close').forEach(close => {
        close.onclick = () => document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    });
    
    // Form submit
    document.getElementById('productForm').onsubmit = (e) => {
        e.preventDefault();
        const product = {
            id: document.getElementById('productId').value,
            name: document.getElementById('productName').value,
            category: document.getElementById('productCategory').value,
            unit: document.getElementById('productUnit').value,
            cost: parseFloat(document.getElementById('productCost').value),
            price: parseFloat(document.getElementById('productPrice').value),
            stock: parseInt(document.getElementById('productStock').value),
            image: null
        };
        if (product.id) {
            const index = products.findIndex(p => p.id === product.id);
            products[index] = { ...products[index], ...product };
        } else {
            addProduct(product);
        }
        saveData();
        renderProducts();
        document.getElementById('productModal').style.display = 'none';
    };
    
    // Export
    document.getElementById('exportExcelBtn').onclick = exportToExcel;
    document.getElementById('exportPdfBtn').onclick = exportToPDF;
    document.getElementById('clearDataBtn').onclick = () => {
        if (confirm('Tüm veriler silinecek! Emin misiniz?')) {
            localStorage.clear();
            location.reload();
        }
    };
    
    // Search & filter
    document.getElementById('productSearch')?.addEventListener('input', renderProducts);
    document.getElementById('categoryFilter')?.addEventListener('change', renderProducts);
}

// Request notification permission
if ('Notification' in window) {
    Notification.requestPermission();
}

// Initialize
initEventListeners();
renderDashboard();
checkLowStock();
setInterval(checkLowStock, 3600000); // Check every hour
