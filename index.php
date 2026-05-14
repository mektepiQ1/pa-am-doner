<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>PazarYo - Pazarcı Yönetim Sistemi</title>
    <!-- PWA için manifest (basit) -->
    <link rel="manifest" href="data:application/manifest+json,{%22name%22:%22PazarYo%22,%22short_name%22:%22PazarYo%22,%22start_url%22:%22.%22,%22display%22:%22standalone%22,%22background_color%22:%22%231b5e20%22,%22theme_color%22:%22%23ff9800%22,%22icons%22:[{%22src%22:%22pazar.jpg%22,%22sizes%22:%22512x512%22,%22type%22:%22image/jpeg%22}]}">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
            transition: background 0.2s ease, color 0.2s ease;
        }

        body {
            background: #f5f7f0;
            color: #1e2a1e;
            padding-bottom: 70px;
        }

        /* Dark Mode */
        body.dark {
            background: #121212;
            color: #e0e0e0;
        }
        body.dark .card, body.dark .top-bar, body.dark .bottom-nav, body.dark .modal-content, body.dark input, body.dark select, body.dark textarea {
            background: #1e1e1e;
            color: #f0f0f0;
            border-color: #444;
        }
        body.dark input, body.dark select, body.dark textarea {
            background: #2c2c2c;
        }
        body.dark .card h3, body.dark .card p {
            color: #ddd;
        }

        /* Layout */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 16px;
        }
        .top-bar {
            background: #2e7d32;
            color: white;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-area img {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            object-fit: cover;
        }
        .page-title {
            font-weight: bold;
            font-size: 1.3rem;
        }
        .dark-toggle {
            background: #ffb74d;
            border: none;
            padding: 8px 12px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 1rem;
            color: #2e3b2e;
        }
        /* Bottom Nav */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            display: flex;
            justify-content: space-around;
            padding: 8px 12px;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
            border-radius: 30px 30px 0 0;
            z-index: 200;
        }
        body.dark .bottom-nav {
            background: #1e2a1e;
        }
        .nav-item {
            text-align: center;
            padding: 6px 12px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 0.75rem;
            color: #5d6b5d;
            flex: 1;
        }
        .nav-item i {
            font-size: 1.6rem;
            display: block;
            margin-bottom: 4px;
        }
        .nav-item.active {
            background: #ff9800;
            color: white;
        }
        body.dark .nav-item.active {
            background: #ffb74d;
            color: #1e2a1e;
        }

        /* Cards & Grid */
        .card {
            background: white;
            border-radius: 28px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.05);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2,1fr);
            gap: 12px;
            margin-top: 12px;
        }
        .stat-card {
            background: #fef7e0;
            border-radius: 20px;
            padding: 14px;
            text-align: center;
        }
        body.dark .stat-card {
            background: #2c2c2c;
        }
        button, .btn {
            background: #ff9800;
            border: none;
            padding: 10px 16px;
            border-radius: 40px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
            color: #2c3e2c;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #ff9800;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin: 6px 0;
            border-radius: 28px;
            border: 1px solid #ccc;
            background: white;
        }
        .product-item, .supplier-item, .expense-item {
            border-bottom: 1px solid #eee;
            padding: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-box {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .badge {
            background: #ff9800;
            border-radius: 50px;
            padding: 4px 10px;
            font-size: 0.7rem;
        }
        .warning {
            color: #d32f2f;
        }
        i {
            margin-right: 5px;
        }
        .modal {
            display: none;
            position: fixed;
            top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 32px;
            padding: 20px;
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="logo-area">
            <img src="pazar.jpg" alt="logo" id="appLogo" onerror="this.src='https://placehold.co/100x100?text=P'">
            <span class="page-title" id="pageTitle">PazarYo</span>
        </div>
        <button class="dark-toggle" id="darkModeToggle"><i class="fas fa-moon"></i> Koyu</button>
    </div>

    <div class="container" id="appContainer">
        <!-- Dinamik sayfalar burada render edilecek -->
    </div>

    <div class="bottom-nav" id="bottomNav">
        <div class="nav-item" data-page="dashboard"><i class="fas fa-chart-line"></i><span>Dashboard</span></div>
        <div class="nav-item" data-page="products"><i class="fas fa-apple-alt"></i><span>Ürünler</span></div>
        <div class="nav-item" data-page="suppliers"><i class="fas fa-truck"></i><span>Halciler</span></div>
        <div class="nav-item" data-page="expenses"><i class="fas fa-receipt"></i><span>Giderler</span></div>
        <div class="nav-item" data-page="income"><i class="fas fa-coins"></i><span>Gelirler</span></div>
        <div class="nav-item" data-page="reports"><i class="fas fa-file-alt"></i><span>Rapor</span></div>
    </div>

    <div id="modal" class="modal"></div>

    <script>
        // ---------- VERİ YAPISI ----------
        let appData = {
            products: [],      // { id, name, category, costPrice, salePrice, stock, unit, image }
            suppliers: [],     // { id, name, productId, date, quantity, pricePerUnit, totalCost }
            expenses: [],      // { id, type, description, date, amount }
            incomes: []        // { id, date, salesAmount, extraAmount, note, soldProducts: [{productId, qty, revenue}] }
        };

        // LocalStorage
        function loadData() {
            const stored = localStorage.getItem("pazaryo_data");
            if(stored) {
                appData = JSON.parse(stored);
            } else {
                // demo veri
                appData.products = [
                    { id: "p1", name: "Domates", category: "Sebze", costPrice: 12, salePrice: 20, stock: 45, unit: "kg", image: "" },
                    { id: "p2", name: "Elma", category: "Meyve", costPrice: 15, salePrice: 25, stock: 12, unit: "kg", image: "" }
                ];
                appData.suppliers = [{ id: "s1", name: "Halit Bey", productId: "p1", date: "2025-03-01", quantity: 50, pricePerUnit: 12, totalCost: 600 }];
                appData.expenses = [{ id: "e1", type: "Yakıt", description: "Mazot", date: "2025-03-10", amount: 300 }];
                appData.incomes = [{ id: "i1", date: "2025-03-10", salesAmount: 1250, extraAmount: 50, note: "", soldProducts: [] }];
                saveData();
            }
        }
        function saveData() {
            localStorage.setItem("pazaryo_data", JSON.stringify(appData));
        }

        // sayfa yönetimi
        let currentPage = "dashboard";
        let salesChart = null;

        function renderPage() {
            const container = document.getElementById("appContainer");
            if(currentPage === "dashboard") renderDashboard(container);
            else if(currentPage === "products") renderProducts(container);
            else if(currentPage === "suppliers") renderSuppliers(container);
            else if(currentPage === "expenses") renderExpenses(container);
            else if(currentPage === "income") renderIncome(container);
            else if(currentPage === "reports") renderReports(container);
            document.querySelectorAll(".nav-item").forEach(el => {
                if(el.dataset.page === currentPage) el.classList.add("active");
                else el.classList.remove("active");
            });
            document.getElementById("pageTitle").innerText = currentPage === "dashboard" ? "PazarYo" : currentPage.charAt(0).toUpperCase() + currentPage.slice(1);
            checkLowStockNotification();
        }

        // ---------- DASHBOARD ----------
        function calculateStats() {
            const now = new Date();
            const todayStr = now.toISOString().slice(0,10);
            const weekAgo = new Date(now.setDate(now.getDate()-7)).toISOString().slice(0,10);
            const monthAgo = new Date(now.setMonth(now.getMonth()-1)).toISOString().slice(0,10);
            now.setDate(now.getDate()+7); // reset

            let totalIncome = 0, totalExpense = 0;
            let dailyIncome=0, weeklyIncome=0, monthlyIncome=0;
            let dailyExpense=0, weeklyExpense=0, monthlyExpense=0;

            appData.incomes.forEach(inc => {
                const incTotal = inc.salesAmount + (inc.extraAmount || 0);
                totalIncome += incTotal;
                if(inc.date === todayStr) dailyIncome += incTotal;
                if(inc.date >= weekAgo) weeklyIncome += incTotal;
                if(inc.date >= monthAgo) monthlyIncome += incTotal;
            });
            appData.expenses.forEach(exp => {
                totalExpense += exp.amount;
                if(exp.date === todayStr) dailyExpense += exp.amount;
                if(exp.date >= weekAgo) weeklyExpense += exp.amount;
                if(exp.date >= monthAgo) monthlyExpense += exp.amount;
            });
            const dailyProfit = dailyIncome - dailyExpense;
            const weeklyProfit = weeklyIncome - weeklyExpense;
            const monthlyProfit = monthlyIncome - monthlyExpense;

            // En çok satan ürün (soldProducts)
            let salesCount = {};
            appData.incomes.forEach(inc => {
                if(inc.soldProducts) inc.soldProducts.forEach(sp => {
                    salesCount[sp.productId] = (salesCount[sp.productId] || 0) + sp.qty;
                });
            });
            let bestProduct = { id: null, qty: 0 };
            for(let pid in salesCount) if(salesCount[pid] > bestProduct.qty) bestProduct = { id: pid, qty: salesCount[pid] };
            let bestProductName = bestProduct.id ? (appData.products.find(p=>p.id===bestProduct.id)?.name || "Yok") : "Yok";

            // En karlı ürün: toplam satış geliri - maliyet
            let profitMap = {};
            appData.incomes.forEach(inc => {
                if(inc.soldProducts) inc.soldProducts.forEach(sp => {
                    const prod = appData.products.find(p=>p.id===sp.productId);
                    if(prod){
                        const revenue = sp.revenue || (sp.qty * prod.salePrice);
                        const cost = sp.qty * prod.costPrice;
                        profitMap[sp.productId] = (profitMap[sp.productId] || 0) + (revenue - cost);
                    }
                });
            });
            let mostProfitable = { id: null, profit: -Infinity };
            for(let pid in profitMap) if(profitMap[pid] > mostProfitable.profit) mostProfitable = { id: pid, profit: profitMap[pid] };
            let profitableName = mostProfitable.id ? appData.products.find(p=>p.id===mostProfitable.id)?.name : "Yok";

            // En fazla zarar
            let lossMap = {};
            for(let pid in profitMap) if(profitMap[pid] < 0) lossMap[pid] = profitMap[pid];
            let mostLoss = { id: null, loss: 0 };
            for(let pid in lossMap) if(lossMap[pid] < mostLoss.loss) mostLoss = { id: pid, loss: lossMap[pid] };
            let lossName = mostLoss.id ? appData.products.find(p=>p.id===mostLoss.id)?.name : "Yok";

            return { totalIncome, totalExpense, dailyProfit, weeklyProfit, monthlyProfit, bestProductName, profitableName, lossName, dailyIncome, dailyExpense };
        }

        function renderDashboard(container) {
            const stats = calculateStats();
            // Günlük satış grafik verisi (son 7 gün)
            let last7Days = [];
            for(let i=6;i>=0;i--){
                let d = new Date(); d.setDate(d.getDate()-i);
                last7Days.push(d.toISOString().slice(0,10));
            }
            let salesByDay = last7Days.map(day => {
                let total = 0;
                appData.incomes.forEach(inc => { if(inc.date === day) total += inc.salesAmount + (inc.extraAmount||0); });
                return total;
            });
            setTimeout(() => {
                const ctx = document.getElementById('dailySalesChart')?.getContext('2d');
                if(ctx) {
                    if(salesChart) salesChart.destroy();
                    salesChart = new Chart(ctx, { type: 'line', data: { labels: last7Days, datasets: [{ label: 'Günlük Satış (TL)', data: salesByDay, borderColor: '#ff9800', tension: 0.3 }] } });
                }
            }, 50);
            container.innerHTML = `
                <div class="card"><h3><i class="fas fa-chart-simple"></i> Özet</h3>
                <div class="stats-grid">
                    <div class="stat-card"><i class="fas fa-lira-sign"></i> Toplam Gelir<br><b>${stats.totalIncome}₺</b></div>
                    <div class="stat-card"><i class="fas fa-truck"></i> Toplam Gider<br><b>${stats.totalExpense}₺</b></div>
                    <div class="stat-card"><i class="fas fa-sun"></i> Günlük Kar/Zarar<br><b>${stats.dailyProfit}₺</b></div>
                    <div class="stat-card"><i class="fas fa-calendar-week"></i> Haftalık Kar/Zarar<br><b>${stats.weeklyProfit}₺</b></div>
                    <div class="stat-card"><i class="fas fa-calendar-alt"></i> Aylık Kar/Zarar<br><b>${stats.monthlyProfit}₺</b></div>
                    <div class="stat-card"><i class="fas fa-chart-line"></i> En Çok Satılan<br><b>${stats.bestProductName}</b></div>
                    <div class="stat-card"><i class="fas fa-chart-line"></i> En Karlı Ürün<br><b>${stats.profitableName}</b></div>
                    <div class="stat-card"><i class="fas fa-chart-line"></i> En Zararlı<br><b>${stats.lossName}</b></div>
                </div></div>
                <div class="card"><h3><i class="fas fa-chart-line"></i> Günlük Satış Grafiği</h3><canvas id="dailySalesChart" width="400" height="200"></canvas></div>
                <div class="card"><h3><i class="fas fa-bell"></i> Düşük Stok Uyarısı</h3><div id="lowStockList"></div></div>
            `;
            const lowStockDiv = document.getElementById("lowStockList");
            if(lowStockDiv){
                const low = appData.products.filter(p => p.stock < 5);
                if(low.length) lowStockDiv.innerHTML = low.map(p => `<div>⚠️ ${p.name} (Stok: ${p.stock})</div>`).join('');
                else lowStockDiv.innerHTML = "<div>✅ Stoklar yeterli</div>";
            }
        }

        function checkLowStockNotification(){
            if(currentPage !== "dashboard"){
                const low = appData.products.filter(p=>p.stock<5);
                if(low.length) showModalMessage("Stok Uyarısı", `${low.length} ürünün stoğu 5'in altında!`, "warning");
            }
        }
        function showModalMessage(title, msg, type="info"){
            const modalDiv = document.getElementById("modal");
            modalDiv.innerHTML = `<div class="modal-content"><h3>${title}</h3><p>${msg}</p><button class="btn" onclick="closeModal()">Kapat</button></div>`;
            modalDiv.style.display = "flex";
        }
        window.closeModal = function(){ document.getElementById("modal").style.display = "none"; };

        // ---------- ÜRÜN YÖNETİMİ ----------
        function renderProducts(container){
            let filterText = "";
            const renderList = (search) => {
                let filtered = appData.products.filter(p => p.name.toLowerCase().includes(search.toLowerCase()));
                container.innerHTML = `
                    <div class="card"><div class="flex-between"><h3><i class="fas fa-box"></i> Ürünler</h3><button class="btn" onclick="openProductModal()"><i class="fas fa-plus"></i> Ekle</button></div>
                    <div class="search-box"><input type="text" id="productSearch" placeholder="Ürün ara..." value="${search}"><button onclick="searchProducts()"><i class="fas fa-search"></i></button></div>
                    <div id="productsList"></div></div>
                `;
                const listDiv = document.getElementById("productsList");
                listDiv.innerHTML = filtered.map(p => `
                    <div class="product-item"><div><b>${p.name}</b><br>${p.category} | Stok: ${p.stock} ${p.unit}<br>Maliyet:${p.costPrice}₺ Satış:${p.salePrice}₺</div>
                    <div><button class="btn-outline" onclick="editProduct('${p.id}')"><i class="fas fa-edit"></i></button> <button onclick="deleteProduct('${p.id}')"><i class="fas fa-trash"></i></button></div></div>
                `).join('');
                document.getElementById("productSearch").addEventListener("input", (e) => renderList(e.target.value));
            };
            renderList("");
            window.searchProducts = () => { const val = document.getElementById("productSearch").value; renderList(val); };
        }
        window.openProductModal = (id=null) => {
            const product = id ? appData.products.find(p=>p.id===id) : null;
            const modalDiv = document.getElementById("modal");
            modalDiv.innerHTML = `<div class="modal-content"><h3>${product ? "Ürün Düzenle" : "Yeni Ürün"}</h3>
                <input id="prodName" placeholder="Ürün adı" value="${product ? product.name : ''}">
                <select id="prodCategory"><option>Sebze</option><option>Meyve</option><option>Diğer</option></select>
                <input id="prodCost" type="number" placeholder="Maliyet (TL)" value="${product ? product.costPrice : ''}">
                <input id="prodSale" type="number" placeholder="Satış Fiyatı" value="${product ? product.salePrice : ''}">
                <input id="prodStock" type="number" placeholder="Stok" value="${product ? product.stock : ''}">
                <select id="prodUnit"><option>kg</option><option>gram</option><option>adet</option><option>kasa</option><option>litre</option></select>
                <button onclick="saveProduct('${id || ''}')">Kaydet</button><button onclick="closeModal()">İptal</button>
            </div>`;
            document.getElementById("prodCategory").value = product ? product.category : "Sebze";
            document.getElementById("prodUnit").value = product ? product.unit : "kg";
            modalDiv.style.display = "flex";
        };
        window.saveProduct = (id) => {
            const name = document.getElementById("prodName").value;
            if(!name) return;
            const product = { id: id || Date.now().toString(), name, category: document.getElementById("prodCategory").value, costPrice: parseFloat(document.getElementById("prodCost").value), salePrice: parseFloat(document.getElementById("prodSale").value), stock: parseInt(document.getElementById("prodStock").value), unit: document.getElementById("prodUnit").value, image: "" };
            if(id){ const index = appData.products.findIndex(p=>p.id===id); if(index!==-1) appData.products[index]=product; }
            else appData.products.push(product);
            saveData(); closeModal(); renderPage();
        };
        window.deleteProduct = (id) => { if(confirm("Sil?")){ appData.products = appData.products.filter(p=>p.id!==id); saveData(); renderPage(); } };
        window.editProduct = (id) => openProductModal(id);
        
        // Halci
        function renderSuppliers(container){
            container.innerHTML = `<div class="card"><div class="flex-between"><h3>Tedarikçiler / Halciler</h3><button class="btn" onclick="openSupplierModal()">+ Ekle</button></div><div id="supplierList"></div></div>`;
            const listDiv = document.getElementById("supplierList");
            listDiv.innerHTML = appData.suppliers.map(s => {
                const prodName = appData.products.find(p=>p.id===s.productId)?.name || "Ürün silinmiş";
                return `<div class="supplier-item"><div><b>${s.name}</b><br>${prodName} | ${s.quantity} adet | ${s.totalCost}₺ (${s.date})</div><div><button onclick="deleteSupplier('${s.id}')">Sil</button></div></div>`;
            }).join('');
        }
        window.openSupplierModal = () => {
            const modalDiv = document.getElementById("modal");
            modalDiv.innerHTML = `<div class="modal-content"><h3>Halci Alım Ekle</h3>
                <input id="supName" placeholder="Halci adı">
                <select id="supProductId">${appData.products.map(p=>`<option value="${p.id}">${p.name}</option>`).join('')}</select>
                <input id="supDate" type="date" value="${new Date().toISOString().slice(0,10)}">
                <input id="supQty" type="number" placeholder="Miktar">
                <input id="supPrice" type="number" placeholder="Birim Fiyat (TL)">
                <button onclick="saveSupplier()">Kaydet</button></div>`;
            modalDiv.style.display = "flex";
        };
        window.saveSupplier = () => {
            const name = document.getElementById("supName").value;
            const productId = document.getElementById("supProductId").value;
            const date = document.getElementById("supDate").value;
            const qty = parseFloat(document.getElementById("supQty").value);
            const price = parseFloat(document.getElementById("supPrice").value);
            if(!name || !qty) return;
            const totalCost = qty * price;
            appData.suppliers.push({ id: Date.now().toString(), name, productId, date, quantity: qty, pricePerUnit: price, totalCost });
            // alınan ürün stoğu artır?
            const prod = appData.products.find(p=>p.id===productId);
            if(prod) prod.stock += qty;
            saveData(); closeModal(); renderPage();
        };
        window.deleteSupplier = (id) => { appData.suppliers = appData.suppliers.filter(s=>s.id!==id); saveData(); renderPage(); };

        // Giderler
        function renderExpenses(container){
            container.innerHTML = `<div class="card"><div class="flex-between"><h3>Giderler</h3><button class="btn" onclick="openExpenseModal()">+ Gider Ekle</button></div><div id="expenseList"></div></div>`;
            document.getElementById("expenseList").innerHTML = appData.expenses.map(e => `<div class="expense-item"><div><b>${e.type}</b> - ${e.description}<br>${e.date} : ${e.amount}₺</div><div><button onclick="deleteExpense('${e.id}')">Sil</button></div></div>`).join('');
        }
        window.openExpenseModal = () => {
            const modalDiv = document.getElementById("modal");
            modalDiv.innerHTML = `<div class="modal-content"><h3>Gider Ekle</h3><select id="expType"><option>Personel</option><option>Yakıt</option><option>Hal</option><option>Nakliye</option><option>Elektrik</option><option>Diğer</option></select><input id="expDesc" placeholder="Açıklama"><input id="expDate" type="date"><input id="expAmount" type="number" placeholder="Tutar"><button onclick="saveExpense()">Kaydet</button></div>`;
            modalDiv.style.display = "flex";
        };
        window.saveExpense = () => {
            const type = document.getElementById("expType").value;
            const desc = document.getElementById("expDesc").value;
            const date = document.getElementById("expDate").value;
            const amount = parseFloat(document.getElementById("expAmount").value);
            if(!amount) return;
            appData.expenses.push({ id: Date.now().toString(), type, description: desc, date, amount });
            saveData(); closeModal(); renderPage();
        };
        window.deleteExpense = (id) => { appData.expenses = appData.expenses.filter(e=>e.id!==id); saveData(); renderPage(); };

        // Gelir
        function renderIncome(container){
            container.innerHTML = `<div class="card"><div class="flex-between"><h3>Günlük Gelir</h3><button class="btn" onclick="openIncomeModal()">+ Gelir Ekle</button></div><div id="incomeList"></div></div>`;
            document.getElementById("incomeList").innerHTML = appData.incomes.map(inc => `<div>${inc.date} | Satış: ${inc.salesAmount}₺ Ek:${inc.extraAmount}₺ <button onclick="deleteIncome('${inc.id}')">Sil</button></div>`).join('');
        }
        window.openIncomeModal = () => {
            const modalDiv = document.getElementById("modal");
            modalDiv.innerHTML = `<div class="modal-content"><h3>Gelir Kaydı</h3><input id="incDate" type="date" value="${new Date().toISOString().slice(0,10)}"><input id="incSales" placeholder="Günlük Satış Geliri (TL)"><input id="incExtra" placeholder="Ek Gelir"><textarea placeholder="Not"></textarea><button onclick="saveIncome()">Kaydet</button></div>`;
            modalDiv.style.display = "flex";
        };
        window.saveIncome = () => {
            const date = document.getElementById("incDate").value;
            const sales = parseFloat(document.getElementById("incSales").value) || 0;
            const extra = parseFloat(document.getElementById("incExtra").value) || 0;
            appData.incomes.push({ id: Date.now().toString(), date, salesAmount: sales, extraAmount: extra, note: "", soldProducts: [] });
            saveData(); closeModal(); renderPage();
        };
        window.deleteIncome = (id) => { appData.incomes = appData.incomes.filter(i=>i.id!==id); saveData(); renderPage(); };

        // Rapor
        function renderReports(container){
            let allRows = [];
            appData.incomes.forEach(i=> allRows.push({tip:"Gelir", tarih:i.date, tutar:i.salesAmount+i.extraAmount}));
            appData.expenses.forEach(e=> allRows.push({tip:"Gider", tarih:e.date, tutar:e.amount}));
            allRows.sort((a,b)=>b.tarih.localeCompare(a.tarih));
            container.innerHTML = `<div class="card"><h3>Rapor Dökümü</h3><button class="btn" onclick="exportExcel()">Excel/CSV Dışa Aktar</button><div style="overflow-x:auto"><table border=1 width=100%><tr><th>Tarih</th><th>Tür</th><th>Tutar</th></tr>${allRows.map(r=>`<tr><td>${r.tarih}</td><td>${r.tip}</td><td>${r.tutar}₺</td></tr>`).join('')}</table></div></div>`;
        }
        window.exportExcel = () => {
            let csv = "Tarih,Tür,Tutar\n";
            [...appData.incomes.map(i=>`${i.date},Gelir,${i.salesAmount+i.extraAmount}`), ...appData.expenses.map(e=>`${e.date},Gider,${e.amount}`)].forEach(row=>csv+=row+"\n");
            const blob = new Blob([csv], {type:"text/csv"}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download="pazaryo_rapor.csv"; a.click();
        };

        // init & navigation
        function init(){
            loadData();
            document.getElementById("darkModeToggle").addEventListener("click",()=>{ document.body.classList.toggle("dark"); localStorage.setItem("darkmode", document.body.classList.contains("dark")); });
            if(localStorage.getItem("darkmode")==="true") document.body.classList.add("dark");
            document.querySelectorAll(".nav-item").forEach(btn => btn.addEventListener("click", (e) => { currentPage = btn.dataset.page; renderPage(); }));
            renderPage();
        }
        init();
    </script>
</body>
</html>
