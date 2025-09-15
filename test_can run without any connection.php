
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Manager Pro</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-gradient: linear-gradient(135deg, #4361ee, #3a0ca3);
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #ff9e00;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --card-shadow: 0 4px 8px rgba(67, 97, 238, 0.12);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            font-size: 0.9rem;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background: var(--primary-gradient);
            color: white;
            padding: 0.8rem 0;
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logo i {
            font-size: 1.2rem;
        }
        
        .auth-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 3px 8px rgba(67, 97, 238, 0.25);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1.5px solid white;
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
            box-shadow: 0 3px 8px rgba(247, 37, 133, 0.25);
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-success {
            background-color: #2ecc71;
            color: white;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .welcome-section {
            text-align: center;
            padding: 1.8rem 1.5rem;
            background: white;
            margin: 1.5rem 0;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }
        
        .welcome-section h1 {
            font-size: 1.6rem;
            margin-bottom: 0.4rem;
            color: var(--secondary);
        }
        
        .welcome-section p {
            font-size: 0.95rem;
            color: var(--gray);
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 1.5rem 0;
        }
        
        @media (max-width: 900px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary);
        }
        
        .card:hover {
            transform: translateY(-3px);
        }
        
        .income-card {
            border-left-color: var(--success);
        }
        
        .expense-card {
            border-left-color: var(--danger);
        }
        
        .card-title {
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .budget-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--dark);
        }
        
        input, select {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }
        
        .category-box {
            margin-top: 1.2rem;
            padding-top: 1.2rem;
            border-top: 1px solid var(--light-gray);
        }
        
        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .items-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .item-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .item-name {
            flex: 2;
        }
        
        .item-amount {
            flex: 1;
        }
        
        .item-actions {
            flex: 0.5;
        }
        
        .total-row {
            font-weight: 700;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid var(--light-gray);
            font-size: 1rem;
            color: var(--dark);
        }
        
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 2.5rem;
        }
        
        @media (max-width: 1000px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }
        
        .chart-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            border-left: 4px solid var(--secondary);
        }
        
        .export-options {
            display: flex;
            gap: 12px;
            margin: 2.5rem 0;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }
        
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 400px;
            box-shadow: var(--box-shadow);
            position: relative;
            animation: modalFadeIn 0.3s ease-out;
            border-top: 4px solid var(--primary);
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-title {
            margin-bottom: 1.2rem;
            color: var(--primary);
            text-align: center;
            font-size: 1.5rem;
        }
        
        .close {
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            color: var(--gray);
            transition: color 0.3s ease;
        }
        
        .close:hover {
            color: var(--danger);
        }
        
        .guest-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: var(--border-radius);
            margin: 1.2rem 0;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.85rem;
        }
        
        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: var(--gray);
            transition: color 0.3s ease;
        }
        
        .icon-btn:hover {
            color: var(--danger);
        }
        
        .form-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 1.2rem;
        }
        
        .form-icon i {
            font-size: 2.5rem;
            color: var(--primary);
            background: var(--light);
            padding: 0.8rem;
            border-radius: 50%;
        }
        
        .modal .form-group {
            margin-bottom: 1rem;
        }
        
        .modal-btn {
            width: 100%;
            justify-content: center;
            padding: 10px;
            margin-top: 8px;
        }
        
        .switch-form {
            text-align: center;
            margin-top: 1.2rem;
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .switch-form a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        
        .switch-form a:hover {
            text-decoration: underline;
        }
        
        .floating-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.35);
            cursor: pointer;
            z-index: 99;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        
        .floating-btn:hover {
            transform: translateY(-3px) rotate(8deg);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.45);
        }
        
        /* Data Table Styles */
        .data-table-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-top: 2.5rem;
            border-left: 4px solid var(--warning);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.85rem;
        }
        
        .data-table th, .data-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .data-table th {
            background-color: var(--light);
            font-weight: 600;
            color: var(--dark);
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
            font-weight: 700;
            background-color: var(--light);
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .income-row {
            color: #27ae60;
        }
        
        .expense-row {
            color: #e74c3c;
        }
        
        .total-row-table {
            font-size: 1rem;
        }
        
        .balance-positive {
            color: #27ae60;
        }
        
        .balance-negative {
            color: #e74c3c;
        }
        
        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 18px;
            border-radius: 6px;
            color: white;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-size: 0.9rem;
        }
        
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .notification.success {
            background-color: #2ecc71;
        }
        
        .notification.error {
            background-color: #e74c3c;
        }
        
        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-left: 3px solid var(--primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
           100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-wallet"></i>
                <span>Budget Manager Pro</span>
            </div>
            <div class="auth-buttons">
                <button id="loginBtn" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
                <button id="registerBtn" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i>
                    Register
                </button>
                <button id="logoutBtn" class="btn btn-danger" style="display: none;">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1 id="welcomeMessage">Welcome to Budget Manager Pro!</h1>
            <p id="userStatus">You are currently using the app as a guest. <a href="#" id="authLink">Login or register</a> to save your data.</p>
        </div>

        <div id="guestWarning" class="guest-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Guest Mode:</strong> Your data will not be saved permanently. Please login or register to save your budget data.
        </div>

        <div class="dashboard">
            <div class="card income-card">
                <h2 class="card-title">
                    <i class="fas fa-money-bill-wave"></i>
                    Income
                </h2>
                <form id="incomeForm" class="budget-form">
                    <div class="form-group">
                        <label for="incomeCategory">Category Name</label>
                        <input type="text" id="incomeCategory" placeholder="e.g., Salary, Freelance" required>
                    </div>
                    <div class="category-box">
                        <div class="category-header">
                            <h3>Income Items</h3>
                            <button type="button" id="addIncomeItem" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Add Item
                            </button>
                        </div>
                        <div id="incomeItems" class="items-list">
                            <div class="item-row">
                                <input type="text" class="item-name" placeholder="Item name">
                                <input type="number" class="item-amount" placeholder="Amount" min="0" step="0.01">
                                <button type="button" class="icon-btn remove-item">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="total-row">
                            Total Income: <span id="incomeTotal">$0.00</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Income
                    </button>
                </form>
            </div>

            <div class="card expense-card">
                <h2 class="card-title">
                    <i class="fas fa-credit-card"></i>
                    Expenses
                </h2>
                <form id="expenseForm" class="budget-form">
                    <div class="form-group">
                        <label for="expenseCategory">Category Name</label>
                        <input type="text" id="expenseCategory" placeholder="e.g., Food, Transport" required>
                    </div>
                    <div class="category-box">
                        <div class="category-header">
                            <h3>Expense Items</h3>
                            <button type="button" id="addExpenseItem" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Add Item
                            </button>
                        </div>
                        <div id="expenseItems" class="items-list">
                            <div class="item-row">
                                <input type="text" class="item-name" placeholder="Item name">
                                <input type="number" class="item-amount" placeholder="Amount" min="0" step="0.01">
                                <button type="button" class="icon-btn remove-item">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="total-row">
                            Total Expenses: <span id="expenseTotal">$0.00</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Expenses
                    </button>
                </form>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="data-table-container">
            <h2 class="card-title">
                <i class="fas fa-table"></i>
                Financial Overview
            </h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Item</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    <!-- Data will be populated here -->
                    <tr>
                        <td colspan="6" style="text-align: center;">No data available. Add income or expenses to see them here.</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row-table">
                        <td colspan="3">Total</td>
                        <td id="netTotal">$0.00</td>
                        <td colspan="2" id="balanceStatus"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h2 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Income Distribution
                </h2>
                <canvas id="incomeChart"></canvas>
            </div>
            <div class="chart-card">
                <h2 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Expense Distribution
                </h2>
                <canvas id="expenseChart"></canvas>
            </div>
        </div>

        <div class="export-options">
            <button id="generatePdf" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i>
                Generate PDF Report
            </button>
            <button id="exportExcel" class="btn btn-primary">
                <i class="fas fa-file-excel"></i>
                Export to Excel
            </button>
            <button id="importExcel" class="btn btn-warning">
                <i class="fas fa-file-import"></i>
                Import from Excel
            </button>
            <input type="file" id="excelFile" accept=".xlsx, .xls" style="display: none;">
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="form-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2 class="modal-title">Login</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" required>
                </div>
                <button type="submit" class="btn btn-primary modal-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </form>
            <div class="switch-form">
                Don't have an account? <a id="switchToRegister">Register now</a>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="form-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 class="modal-title">Register</h2>
            <form id="registerForm">
                <div class="form-group">
                    <label for="registerName">Full Name</label>
                    <input type="text" id="registerName" required>
                </div>
                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" id="registerEmail" required>
                </div>
                <div class="form-group">
                    <label for="registerPassword">Password</label>
                    <input type="password" id="registerPassword" required>
                </div>
                <div class="form-group">
                    <label for="registerConfirmPassword">Confirm Password</label>
                    <input type="password" id="registerConfirmPassword" required>
                </div>
                <button type="submit" class="btn btn-primary modal-btn">
                    <i class="fas fa-user-plus"></i>
                    Register
                </button>
            </form>
            <div class="switch-form">
                Already have an account? <a id="switchToLogin">Login now</a>
            </div>
        </div>
    </div>

    <div class="floating-btn" id="helpBtn">
        <i class="fas fa-question"></i>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <script>
        // Data structure to store budget information
        let budgetData = {
            income: [],
            expenses: [],
            user: null
        };

        // Chart instances
        let incomeChart, expenseChart;

        // DOM Elements
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const logoutBtn = document.getElementById('logoutBtn');
        const welcomeMessage = document.getElementById('welcomeMessage');
        const userStatus = document.getElementById('userStatus');
        const authLink = document.getElementById('authLink');
        const guestWarning = document.getElementById('guestWarning');
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const closeButtons = document.querySelectorAll('.close');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const incomeForm = document.getElementById('incomeForm');
        const expenseForm = document.getElementById('expenseForm');
        const addIncomeItem = document.getElementById('addIncomeItem');
        const addExpenseItem = document.getElementById('addExpenseItem');
        const incomeItems = document.getElementById('incomeItems');
        const expenseItems = document.getElementById('expenseItems');
        const incomeTotal = document.getElementById('incomeTotal');
        const expenseTotal = document.getElementById('expenseTotal');
        const generatePdf = document.getElementById('generatePdf');
        const exportExcel = document.getElementById('exportExcel');
        const importExcel = document.getElementById('importExcel');
        const excelFile = document.getElementById('excelFile');
        const switchToRegister = document.getElementById('switchToRegister');
        const switchToLogin = document.getElementById('switchToLogin');
        const dataTableBody = document.getElementById('dataTableBody');
        const netTotal = document.getElementById('netTotal');
        const balanceStatus = document.getElementById('balanceStatus');
        const notification = document.getElementById('notification');
        const helpBtn = document.getElementById('helpBtn');

        // Event Listeners
        loginBtn.addEventListener('click', () => loginModal.style.display = 'flex');
        registerBtn.addEventListener('click', () => registerModal.style.display = 'flex');
        logoutBtn.addEventListener('click', handleLogout);
        authLink.addEventListener('click', (e) => {
            e.preventDefault();
            loginModal.style.display = 'flex';
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                loginModal.style.display = 'none';
                registerModal.style.display = 'none';
            });
        });

        switchToRegister.addEventListener('click', () => {
            loginModal.style.display = 'none';
            registerModal.style.display = 'flex';
        });

        switchToLogin.addEventListener('click', () => {
            registerModal.style.display = 'none';
            loginModal.style.display = 'flex';
        });

        window.addEventListener('click', (e) => {
            if (e.target === loginModal) loginModal.style.display = 'none';
            if (e.target === registerModal) registerModal.style.display = 'none';
        });

        loginForm.addEventListener('submit', handleLogin);
        registerForm.addEventListener('submit', handleRegister);
        incomeForm.addEventListener('submit', handleIncomeSubmit);
        expenseForm.addEventListener('submit', handleExpenseSubmit);
        addIncomeItem.addEventListener('click', () => addItemRow(incomeItems));
        addExpenseItem.addEventListener('click', () => addItemRow(expenseItems));
        generatePdf.addEventListener('click', generatePdfReport);
        exportExcel.addEventListener('click', exportToExcel);
        importExcel.addEventListener('click', () => excelFile.click());
        excelFile.addEventListener('change', importFromExcel);
        helpBtn.addEventListener('click', showHelp);

        // Delegate event listeners for dynamic elements
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item') || e.target.parentElement.classList.contains('remove-item')) {
                const itemRow = e.target.closest('.item-row');
                if (itemRow.parentElement.childElementCount > 1) {
                    itemRow.remove();
                    calculateTotal(itemRow.closest('.category-box'));
                }
            }
            
            // Handle delete buttons in the data table
            if (e.target.classList.contains('delete-item')) {
                const id = e.target.getAttribute('data-id');
                const type = e.target.getAttribute('data-type');
                deleteBudgetItem(id, type);
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('item-amount')) {
                calculateTotal(e.target.closest('.category-box'));
            }
        });

        // Initialize the app
        function init() {
            // Check if user is logged in
            const savedUser = localStorage.getItem('budgetUser');
            if (savedUser) {
                budgetData.user = JSON.parse(savedUser);
                updateUIAfterAuth();
            }
            
            // Initialize charts
            initCharts();
            
            // Load any saved data
            const savedData = localStorage.getItem('budgetData');
            if (savedData) {
                const parsedData = JSON.parse(savedData);
                budgetData.income = parsedData.income || [];
                budgetData.expenses = parsedData.expenses || [];
                updateCharts();
                updateDataTable();
            }
        }

        // Authentication functions
        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            // Show loading state
            const loginBtn = document.querySelector('#loginForm button');
            const originalText = loginBtn.innerHTML;
            loginBtn.innerHTML = '<div class="spinner"></div>';
            loginBtn.disabled = true;
            
            // Simulate API call to database
            setTimeout(() => {
                // Check if user exists in localStorage "database"
                const users = JSON.parse(localStorage.getItem('budgetUsers') || '[]');
                const user = users.find(u => u.email === email && u.password === password);
                
                if (user) {
                    budgetData.user = {
                        id: user.id,
                        name: user.name,
                        email: user.email
                    };
                    
                    // Save to localStorage
                    localStorage.setItem('budgetUser', JSON.stringify(budgetData.user));
                    
                    // Load user's data
                    const userData = localStorage.getItem(`budgetData_${user.id}`);
                    if (userData) {
                        const parsedData = JSON.parse(userData);
                        budgetData.income = parsedData.income || [];
                        budgetData.expenses = parsedData.expenses || [];
                        localStorage.setItem('budgetData', JSON.stringify(budgetData));
                    }
                    
                    // Update UI
                    updateUIAfterAuth();
                    loginModal.style.display = 'none';
                    loginForm.reset();
                    
                    // Update charts and table
                    updateCharts();
                    updateDataTable();
                    
                    // Show success message
                    showNotification('Login successful!', 'success');
                } else {
                    showNotification('Invalid email or password!', 'error');
                }
                
                // Restore button
                loginBtn.innerHTML = originalText;
                loginBtn.disabled = false;
            }, 1000);
        }

        function handleRegister(e) {
            e.preventDefault();
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirmPassword').value;
            
            if (password !== confirmPassword) {
                showNotification('Passwords do not match!', 'error');
                return;
            }
            
            // Show loading state
            const registerBtn = document.querySelector('#registerForm button');
            const originalText = registerBtn.innerHTML;
            registerBtn.innerHTML = '<div class="spinner"></div>';
            registerBtn.disabled = true;
            
            // Simulate API call to database
            setTimeout(() => {
                // Check if user already exists
                const users = JSON.parse(localStorage.getItem('budgetUsers') || '[]');
                if (users.some(user => user.email === email)) {
                    showNotification('User with this email already exists!', 'error');
                    registerBtn.innerHTML = originalText;
                    registerBtn.disabled = false;
                    return;
                }
                
                // Create new user
                const newUser = {
                    id: Date.now().toString(),
                    name: name,
                    email: email,
                    password: password
                };
                
                users.push(newUser);
                localStorage.setItem('budgetUsers', JSON.stringify(users));
                
                budgetData.user = {
                    id: newUser.id,
                    name: newUser.name,
                    email: newUser.email
                };
                
                // Save to localStorage
                localStorage.setItem('budgetUser', JSON.stringify(budgetData.user));
                
                // Update UI
                updateUIAfterAuth();
                registerModal.style.display = 'none';
                registerForm.reset();
                
                // Show success message
                showNotification('Registration successful! You are now logged in.', 'success');
                
                // Restore button
                registerBtn.innerHTML = originalText;
                registerBtn.disabled = false;
            }, 1000);
        }

        function handleLogout() {
            // Save user data before logout
            if (budgetData.user) {
                localStorage.setItem(`budgetData_${budgetData.user.id}`, JSON.stringify({
                    income: budgetData.income,
                    expenses: budgetData.expenses
                }));
            }
            
            budgetData.user = null;
            localStorage.removeItem('budgetUser');
            
            // Clear data if in guest mode
            budgetData.income = [];
            budgetData.expenses = [];
            localStorage.removeItem('budgetData');
            
            // Update UI
            welcomeMessage.textContent = 'Welcome to Budget Manager Pro!';
            userStatus.innerHTML = 'You are currently using the app as a guest. <a href="#" id="authLink">Login or register</a> to save your data.';
            document.getElementById('authLink').addEventListener('click', (e) => {
                e.preventDefault();
                loginModal.style.display = 'flex';
            });
            
            loginBtn.style.display = 'block';
            registerBtn.style.display = 'block';
            logoutBtn.style.display = 'none';
            guestWarning.style.display = 'block';
            
            // Update charts and table
            updateCharts();
            updateDataTable();
            
            showNotification('You have been logged out.', 'success');
        }

        function updateUIAfterAuth() {
            welcomeMessage.textContent = `Welcome back, ${budgetData.user.name}!`;
            userStatus.textContent = `You are logged in as ${budgetData.user.email}. Your data will be saved.`;
            loginBtn.style.display = 'none';
            registerBtn.style.display = 'none';
            logoutBtn.style.display = 'block';
            guestWarning.style.display = 'none';
        }

        // Budget functions
        function addItemRow(container) {
            const itemRow = document.createElement('div');
            itemRow.className = 'item-row';
            itemRow.innerHTML = `
                <input type="text" class="item-name" placeholder="Item name">
                <input type="number" class="item-amount" placeholder="Amount" min="0" step="0.01">
                <button type="button" class="icon-btn remove-item">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(itemRow);
        }

        function calculateTotal(categoryBox) {
            const itemRows = categoryBox.querySelectorAll('.item-row');
            let total = 0;
            
            itemRows.forEach(row => {
                const amountInput = row.querySelector('.item-amount');
                if (amountInput.value) {
                    total += parseFloat(amountInput.value);
                }
            });
            
            const totalElement = categoryBox.querySelector('.total-row span');
            totalElement.textContent = `$${total.toFixed(2)}`;
            
            return total;
        }

        function handleIncomeSubmit(e) {
            e.preventDefault();
            const categoryName = document.getElementById('incomeCategory').value;
            const items = [];
            
            incomeItems.querySelectorAll('.item-row').forEach(row => {
                const name = row.querySelector('.item-name').value;
                const amount = row.querySelector('.item-amount').value;
                
                if (name && amount) {
                    items.push({
                        id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
                        name: name,
                        amount: parseFloat(amount),
                        date: new Date().toISOString().split('T')[0]
                    });
                }
            });
            
            if (items.length === 0) {
                showNotification('Please add at least one income item.', 'error');
                return;
            }
            
            // Add to budget data
            const total = calculateTotal(incomeItems.parentElement);
            budgetData.income.push({
                id: Date.now().toString(),
                category: categoryName,
                items: items,
                total: total
            });
            
            // Save to localStorage
            if (budgetData.user) {
                localStorage.setItem(`budgetData_${budgetData.user.id}`, JSON.stringify({
                    income: budgetData.income,
                    expenses: budgetData.expenses
                }));
            }
            localStorage.setItem('budgetData', JSON.stringify(budgetData));
            
            // Update charts and table
            updateCharts();
            updateDataTable();
            
            // Reset form
            incomeForm.reset();
            incomeItems.innerHTML = '';
            addItemRow(incomeItems);
            incomeTotal.textContent = '$0.00';
            
            showNotification('Income category added successfully!', 'success');
        }

        function handleExpenseSubmit(e) {
            e.preventDefault();
            const categoryName = document.getElementById('expenseCategory').value;
            const items = [];
            
            expenseItems.querySelectorAll('.item-row').forEach(row => {
                const name = row.querySelector('.item-name').value;
                const amount = row.querySelector('.item-amount').value;
                
                if (name && amount) {
                    items.push({
                        id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
                        name: name,
                        amount: parseFloat(amount),
                        date: new Date().toISOString().split('T')[0]
                    });
                }
            });
            
            if (items.length === 0) {
                showNotification('Please add at least one expense item.', 'error');
                return;
            }
            
            // Add to budget data
            const total = calculateTotal(expenseItems.parentElement);
            budgetData.expenses.push({
                id: Date.now().toString(),
                category: categoryName,
                items: items,
                total: total
            });
            
            // Save to localStorage
            if (budgetData.user) {
                localStorage.setItem(`budgetData_${budgetData.user.id}`, JSON.stringify({
                    income: budgetData.income,
                    expenses: budgetData.expenses
                }));
            }
            localStorage.setItem('budgetData', JSON.stringify(budgetData));
            
            // Update charts and table
            updateCharts();
            updateDataTable();
            
            // Reset form
            expenseForm.reset();
            expenseItems.innerHTML = '';
            addItemRow(expenseItems);
            expenseTotal.textContent = '$0.00';
            
            showNotification('Expense category added successfully!', 'success');
        }

        // Data table functions
        function updateDataTable() {
            // Clear the table
            dataTableBody.innerHTML = '';
            
            let totalIncome = 0;
            let totalExpenses = 0;
            
            // Add income items to the table
            budgetData.income.forEach(category => {
                category.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'income-row';
                    row.innerHTML = `
                        <td>Income</td>
                        <td>${category.category}</td>
                        <td>${item.name}</td>
                        <td>$${item.amount.toFixed(2)}</td>
                        <td>${item.date}</td>
                        <td>
                            <button class="icon-btn delete-item" data-id="${item.id}" data-type="income">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    dataTableBody.appendChild(row);
                    totalIncome += item.amount;
                });
            });
            
            // Add expense items to the table
            budgetData.expenses.forEach(category => {
                category.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'expense-row';
                    row.innerHTML = `
                        <td>Expense</td>
                        <td>${category.category}</td>
                        <td>${item.name}</td>
                        <td>$${item.amount.toFixed(2)}</td>
                        <td>${item.date}</td>
                        <td>
                            <button class="icon-btn delete-item" data-id="${item.id}" data-type="expense">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    dataTableBody.appendChild(row);
                    totalExpenses += item.amount;
                });
            });
            
            // Calculate net total
            const net = totalIncome - totalExpenses;
            netTotal.textContent = `$${net.toFixed(2)}`;
            
            // Update balance status
            if (net > 0) {
                balanceStatus.innerHTML = '<span class="balance-positive">Positive Balance</span>';
            } else if (net < 0) {
                balanceStatus.innerHTML = '<span class="balance-negative">Negative Balance</span>';
            } else {
                balanceStatus.innerHTML = '<span>Break Even</span>';
            }
            
            // If no data, show message
            if (dataTableBody.children.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="6" style="text-align: center;">No data available. Add income or expenses to see them here.</td>
                `;
                dataTableBody.appendChild(row);
            }
        }

        function deleteBudgetItem(id, type) {
            if (type === 'income') {
                budgetData.income.forEach(category => {
                    category.items = category.items.filter(item => item.id !== id);
                });
                // Remove empty categories
                budgetData.income = budgetData.income.filter(category => category.items.length > 0);
            } else if (type === 'expense') {
                budgetData.expenses.forEach(category => {
                    category.items = category.items.filter(item => item.id !== id);
                });
                // Remove empty categories
                budgetData.expenses = budgetData.expenses.filter(category => category.items.length > 0);
            }
            
            // Save to localStorage
            if (budgetData.user) {
                localStorage.setItem(`budgetData_${budgetData.user.id}`, JSON.stringify({
                    income: budgetData.income,
                    expenses: budgetData.expenses
                }));
            }
            localStorage.setItem('budgetData', JSON.stringify(budgetData));
            
            // Update charts and table
            updateCharts();
            updateDataTable();
            
            showNotification('Item deleted successfully!', 'success');
        }

        // Chart functions
        function initCharts() {
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            
            incomeChart = new Chart(incomeCtx, {
                type: 'pie',
                data: {
                    labels: ['No data yet'],
                    datasets: [{
                        data: [1],
                        backgroundColor: [
                            '#4361ee'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Income Distribution'
                        }
                    }
                }
            });
            
            expenseChart = new Chart(expenseCtx, {
                type: 'pie',
                data: {
                    labels: ['No data yet'],
                    datasets: [{
                        data: [1],
                        backgroundColor: [
                            '#f72585'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Expense Distribution'
                        }
                    }
                }
            });
        }

        function updateCharts() {
            // Update income chart
            if (budgetData.income.length > 0) {
                const incomeLabels = budgetData.income.map(item => item.category);
                const incomeData = budgetData.income.map(item => item.total);
                const backgroundColors = generateColors(incomeData.length);
                
                incomeChart.data.labels = incomeLabels;
                incomeChart.data.datasets[0].data = incomeData;
                incomeChart.data.datasets[0].backgroundColor = backgroundColors;
            } else {
                incomeChart.data.labels = ['No data yet'];
                incomeChart.data.datasets[0].data = [1];
                incomeChart.data.datasets[0].backgroundColor = ['#4361ee'];
            }
            
            incomeChart.update();
            
            // Update expense chart
            if (budgetData.expenses.length > 0) {
                const expenseLabels = budgetData.expenses.map(item => item.category);
                const expenseData = budgetData.expenses.map(item => item.total);
                const backgroundColors = generateColors(expenseData.length);
                
                expenseChart.data.labels = expenseLabels;
                expenseChart.data.datasets[0].data = expenseData;
                expenseChart.data.datasets[0].backgroundColor = backgroundColors;
            } else {
                expenseChart.data.labels = ['No data yet'];
                expenseChart.data.datasets[0].data = [1];
                expenseChart.data.datasets[0].backgroundColor = ['#f72585'];
            }
            
            expenseChart.update();
        }

        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = (i * 137.5) % 360; // Golden angle approximation
                colors.push(`hsl(${hue}, 70%, 65%)`);
            }
            return colors;
        }

        // Export functions
        function generatePdfReport() {
            alert('PDF generation would be implemented with a library like jsPDF. This is a demo.');
            // In a real implementation, this would use jsPDF to create a PDF report
        }

        function exportToExcel() {
            if (budgetData.income.length === 0 && budgetData.expenses.length === 0) {
                showNotification('No data to export!', 'error');
                return;
            }
            
            // Prepare data for Excel
            const wb = XLSX.utils.book_new();
            
            // Income data
            const incomeData = [['Category', 'Item', 'Amount', 'Date']];
            budgetData.income.forEach(category => {
                category.items.forEach(item => {
                    incomeData.push([category.category, item.name, item.amount, item.date]);
                });
                incomeData.push([category.category, 'TOTAL', category.total, '']);
            });
            
            // Expense data
            const expenseData = [['Category', 'Item', 'Amount', 'Date']];
            budgetData.expenses.forEach(category => {
                category.items.forEach(item => {
                    expenseData.push([category.category, item.name, item.amount, item.date]);
                });
                expenseData.push([category.category, 'TOTAL', category.total, '']);
            });
            
            // Summary data
            const summaryData = [
                ['Summary'],
                ['Total Income', calculateTotalIncome()],
                ['Total Expenses', calculateTotalExpenses()],
                ['Net Balance', calculateTotalIncome() - calculateTotalExpenses()]
            ];
            
            // Add sheets to workbook
            const incomeWS = XLSX.utils.aoa_to_sheet(incomeData);
            const expenseWS = XLSX.utils.aoa_to_sheet(expenseData);
            const summaryWS = XLSX.utils.aoa_to_sheet(summaryData);
            
            XLSX.utils.book_append_sheet(wb, incomeWS, 'Income');
            XLSX.utils.book_append_sheet(wb, expenseWS, 'Expenses');
            XLSX.utils.book_append_sheet(wb, summaryWS, 'Summary');
            
            // Export to file
            XLSX.writeFile(wb, 'budget_data.xlsx');
            showNotification('Data exported successfully!', 'success');
        }

        function importFromExcel(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                const wb = XLSX.read(data, {type: 'array'});
                
                // Reset current data
                budgetData.income = [];
                budgetData.expenses = [];
                
                // Process income sheet
                if (wb.SheetNames.includes('Income')) {
                    const incomeWS = wb.Sheets['Income'];
                    const incomeJson = XLSX.utils.sheet_to_json(incomeWS, {header: 1});
                    
                    // Skip header row
                    for (let i = 1; i < incomeJson.length; i++) {
                        const row = incomeJson[i];
                        if (row.length >= 3) {
                            const category = row[0];
                            const itemName = row[1];
                            const amount = row[2];
                            const date = row[3] || new Date().toISOString().split('T')[0];
                            
                            if (category && itemName && amount) {
                                // Check if category exists
                                let categoryObj = budgetData.income.find(c => c.category === category);
                                if (!categoryObj) {
                                    categoryObj = {
                                        id: Date.now().toString(),
                                        category: category,
                                        items: [],
                                        total: 0
                                    };
                                    budgetData.income.push(categoryObj);
                                }
                                
                                if (itemName.toLowerCase() !== 'total') {
                                    categoryObj.items.push({
                                        id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
                                        name: itemName,
                                        amount: amount,
                                        date: date
                                    });
                                    categoryObj.total += amount;
                                }
                            }
                        }
                    }
                }
                
                // Process expenses sheet
                if (wb.SheetNames.includes('Expenses')) {
                    const expenseWS = wb.Sheets['Expenses'];
                    const expenseJson = XLSX.utils.sheet_to_json(expenseWS, {header: 1});
                    
                    // Skip header row
                    for (let i = 1; i < expenseJson.length; i++) {
                        const row = expenseJson[i];
                        if (row.length >= 3) {
                            const category = row[0];
                            const itemName = row[1];
                            const amount = row[2];
                            const date = row[3] || new Date().toISOString().split('T')[0];
                            
                            if (category && itemName && amount) {
                                // Check if category exists
                                let categoryObj = budgetData.expenses.find(c => c.category === category);
                                if (!categoryObj) {
                                    categoryObj = {
                                        id: Date.now().toString(),
                                        category: category,
                                        items: [],
                                        total: 0
                                    };
                                    budgetData.expenses.push(categoryObj);
                                }
                                
                                if (itemName.toLowerCase() !== 'total') {
                                    categoryObj.items.push({
                                        id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
                                        name: itemName,
                                        amount: amount,
                                        date: date
                                    });
                                    categoryObj.total += amount;
                                }
                            }
                        }
                    }
                }
                
                // Save to localStorage if user is logged in
                if (budgetData.user) {
                    localStorage.setItem(`budgetData_${budgetData.user.id}`, JSON.stringify({
                        income: budgetData.income,
                        expenses: budgetData.expenses
                    }));
                }
                localStorage.setItem('budgetData', JSON.stringify(budgetData));
                
                // Update charts and table
                updateCharts();
                updateDataTable();
                
                showNotification('Data imported successfully!', 'success');
            };
            reader.readAsArrayBuffer(file);
            
            // Reset file input
            e.target.value = '';
        }

        // Helper functions
        function calculateTotalIncome() {
            return budgetData.income.reduce((total, category) => total + category.total, 0);
        }

        function calculateTotalExpenses() {
            return budgetData.expenses.reduce((total, category) => total + category.total, 0);
        }

        function showNotification(message, type) {
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.className = 'notification';
            }, 3000);
        }

        function showHelp() {
            alert('Budget Manager Pro Help:\n\n1. Add income and expense categories\n2. Add items to each category\n3. View your financial overview in the table\n4. See visual representations in the charts\n5. Export your data to Excel\n6. Register/login to save your data permanently');
        }

        // Initialize the application
        init();
    </script>
</body>
</html>