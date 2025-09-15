
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
    <link rel="stylesheet" href="styles.css">
    
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

   

     <script src="script.js"></script>
</body>
</html>