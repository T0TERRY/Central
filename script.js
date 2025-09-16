// Data structure to store budget information
let budgetData = {
  income: [],
  expenses: [],
  user: null,
};

// Chart instances
let incomeChart, expenseChart;

// DOM Elements
const loginBtn = document.getElementById("loginBtn");
const registerBtn = document.getElementById("registerBtn");
const logoutBtn = document.getElementById("logoutBtn");
const welcomeMessage = document.getElementById("welcomeMessage");
const userStatus = document.getElementById("userStatus");
const authLink = document.getElementById("authLink");
const guestWarning = document.getElementById("guestWarning");
const loginModal = document.getElementById("loginModal");
const registerModal = document.getElementById("registerModal");
const closeButtons = document.querySelectorAll(".close");
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const incomeForm = document.getElementById("incomeForm");
const expenseForm = document.getElementById("expenseForm");
const addIncomeItem = document.getElementById("addIncomeItem");
const addExpenseItem = document.getElementById("addExpenseItem");
const incomeItems = document.getElementById("incomeItems");
const expenseItems = document.getElementById("expenseItems");
const incomeTotal = document.getElementById("incomeTotal");
const expenseTotal = document.getElementById("expenseTotal");
const generatePdf = document.getElementById("generatePdf");
const exportExcel = document.getElementById("exportExcel");
const importExcel = document.getElementById("importExcel");
const excelFile = document.getElementById("excelFile");
const switchToRegister = document.getElementById("switchToRegister");
const switchToLogin = document.getElementById("switchToLogin");
const dataTableBody = document.getElementById("dataTableBody");
const netTotal = document.getElementById("netTotal");
const balanceStatus = document.getElementById("balanceStatus");
const notification = document.getElementById("notification");
const helpBtn = document.getElementById("helpBtn");

// Event Listeners
loginBtn.addEventListener("click", () => (loginModal.style.display = "flex"));
registerBtn.addEventListener(
  "click",
  () => (registerModal.style.display = "flex")
);
logoutBtn.addEventListener("click", handleLogout);
authLink.addEventListener("click", (e) => {
  e.preventDefault();
  loginModal.style.display = "flex";
});

closeButtons.forEach((button) => {
  button.addEventListener("click", () => {
    loginModal.style.display = "none";
    registerModal.style.display = "none";
  });
});

switchToRegister.addEventListener("click", () => {
  loginModal.style.display = "none";
  registerModal.style.display = "flex";
});

switchToLogin.addEventListener("click", () => {
  registerModal.style.display = "none";
  loginModal.style.display = "flex";
});

window.addEventListener("click", (e) => {
  if (e.target === loginModal) loginModal.style.display = "none";
  if (e.target === registerModal) registerModal.style.display = "none";
});

loginForm.addEventListener("submit", handleLogin);
registerForm.addEventListener("submit", handleRegister);
incomeForm.addEventListener("submit", handleIncomeSubmit);
expenseForm.addEventListener("submit", handleExpenseSubmit);
addIncomeItem.addEventListener("click", () => addItemRow(incomeItems));
addExpenseItem.addEventListener("click", () => addItemRow(expenseItems));
generatePdf.addEventListener("click", generatePdfReport);
exportExcel.addEventListener("click", exportToExcel);
importExcel.addEventListener("click", () => excelFile.click());
excelFile.addEventListener("change", importFromExcel);
helpBtn.addEventListener("click", showHelp);

// Delegate event listeners for dynamic elements
document.addEventListener("click", (e) => {
  if (
    e.target.classList.contains("remove-item") ||
    e.target.parentElement.classList.contains("remove-item")
  ) {
    const itemRow = e.target.closest(".item-row");
    if (itemRow.parentElement.childElementCount > 1) {
      itemRow.remove();
      calculateTotal(itemRow.closest(".category-box"));
    }
  }

  // Handle delete buttons in the data table
  if (e.target.closest(".delete-item")) {
    const btn = e.target.closest(".delete-item");
    const id = btn.getAttribute("data-id");
    const category_id = btn.getAttribute("data-category-id");
    const type = btn.getAttribute("data-type");
    console.log(id, type, btn.classList);
    deleteBudgetItem(category_id, id, type);
  }
});

document.addEventListener("input", (e) => {
  if (e.target.classList.contains("item-amount")) {
    calculateTotal(e.target.closest(".category-box"));
  }
});

// Initialize the app
async function init() {
  // Check if user is logged in
  const savedUser = localStorage.getItem("budgetUser");
  if (savedUser) {
    budgetData.user = JSON.parse(savedUser);
    updateUIAfterAuth();

    // Load user data from server
    await loadUserData();
  } else {
    // Load any saved guest data
    const savedData = localStorage.getItem("budgetData");
    if (savedData) {
      const parsedData = JSON.parse(savedData);
      budgetData.income = parsedData.income || [];
      budgetData.expenses = parsedData.expenses || [];
    }
  }

  // Initialize charts
  initCharts();

  // Update UI
  updateCharts();
  updateDataTable();
}

// Authentication functions
async function handleLogin(e) {
  e.preventDefault();
  const email = document.getElementById("loginEmail").value;
  const password = document.getElementById("loginPassword").value;

  // Show loading state
  const loginBtn = document.querySelector("#loginForm button");
  const originalText = loginBtn.innerHTML;
  loginBtn.innerHTML = '<div class="spinner"></div>';
  loginBtn.disabled = true;

  try {
    const formData = new FormData();
    formData.append("action", "login");
    formData.append("email", email);
    formData.append("password", password);

    const response = await fetch("auth.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      budgetData.user = data.user;
      localStorage.setItem("budgetUser", JSON.stringify(budgetData.user));

      // Load user's data from server
      await loadUserData();

      // Update UI
      updateUIAfterAuth();
      loginModal.style.display = "none";
      loginForm.reset();

      // Update charts and table
      updateCharts();
      updateDataTable();

      // Show success message
      showNotification("Login successful!", "success");
    } else {
      showNotification(data.message, "error");
    }
  } catch (error) {
    showNotification("Login failed. Please try again.", "error");
    console.error("Login error:", error);
  } finally {
    // Restore button
    loginBtn.innerHTML = originalText;
    loginBtn.disabled = false;
  }
}

async function handleRegister(e) {
  e.preventDefault();
  const name = document.getElementById("registerName").value;
  const email = document.getElementById("registerEmail").value;
  const password = document.getElementById("registerPassword").value;
  const confirmPassword = document.getElementById(
    "registerConfirmPassword"
  ).value;

  if (password !== confirmPassword) {
    showNotification("Passwords do not match!", "error");
    return;
  }

  // Show loading state
  const registerBtn = document.querySelector("#registerForm button");
  const originalText = registerBtn.innerHTML;
  registerBtn.innerHTML = '<div class="spinner"></div>';
  registerBtn.disabled = true;

  try {
    const formData = new FormData();
    formData.append("action", "register");
    formData.append("name", name);
    formData.append("email", email);
    formData.append("password", password);
    formData.append("confirmPassword", confirmPassword);

    const response = await fetch("auth.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      budgetData.user = data.user;
      localStorage.setItem("budgetUser", JSON.stringify(budgetData.user));

      // Update UI
      updateUIAfterAuth();
      registerModal.style.display = "none";
      registerForm.reset();

      // Show success message
      showNotification(
        "Registration successful! You are now logged in.",
        "success"
      );
    } else {
      showNotification(data.message, "error");
    }
  } catch (error) {
    showNotification("Registration failed. Please try again.", "error");
    console.error("Registration error:", error);
  } finally {
    // Restore button
    registerBtn.innerHTML = originalText;
    registerBtn.disabled = false;
  }
}

async function handleLogout() {
  try {
    const formData = new FormData();
    formData.append("action", "logout");

    await fetch("auth.php", {
      method: "POST",
      body: formData,
    });
  } catch (error) {
    console.error("Logout error:", error);
  } finally {
    // Clear local data
    budgetData.user = null;
    budgetData.income = [];
    budgetData.expenses = [];
    localStorage.removeItem("budgetUser");
    localStorage.removeItem("budgetData");

    // Update UI
    welcomeMessage.textContent = "Welcome to Budget Manager Pro!";
    userStatus.innerHTML =
      'You are currently using the app as a guest. <a href="#" id="authLink">Login or register</a> to save your data.';
    document.getElementById("authLink").addEventListener("click", (e) => {
      e.preventDefault();
      loginModal.style.display = "flex";
    });

    loginBtn.style.display = "block";
    registerBtn.style.display = "block";
    logoutBtn.style.display = "none";
    guestWarning.style.display = "block";

    // Update charts and table
    updateCharts();
    updateDataTable();

    showNotification("You have been logged out.", "success");
  }
}

function updateUIAfterAuth() {
  welcomeMessage.textContent = `Welcome back, ${budgetData.user.name}!`;
  userStatus.textContent = `You are logged in as ${budgetData.user.email}. Your data will be saved.`;
  loginBtn.style.display = "none";
  registerBtn.style.display = "none";
  logoutBtn.style.display = "block";
  guestWarning.style.display = "none";
}

// Budget functions
function addItemRow(container) {
  const itemRow = document.createElement("div");
  itemRow.className = "item-row";
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
  const itemRows = categoryBox.querySelectorAll(".item-row");
  let total = 0;

  itemRows.forEach((row) => {
    const amountInput = row.querySelector(".item-amount");
    if (amountInput.value) {
      total += parseFloat(amountInput.value);
    }
  });

  const totalElement = categoryBox.querySelector(".total-row span");
  totalElement.textContent = `$${total.toFixed(2)}`;

  return total;
}

async function handleIncomeSubmit(e) {
  e.preventDefault();
  const categoryName = document.getElementById("incomeCategory").value;
  const items = [];

  incomeItems.querySelectorAll(".item-row").forEach((row) => {
    const name = row.querySelector(".item-name").value;
    const amount = row.querySelector(".item-amount").value;

    if (name && amount) {
      items.push({
        name: name,
        amount: parseFloat(amount),
        date: new Date().toISOString().split("T")[0],
      });
    }
  });

  if (items.length === 0) {
    showNotification("Please add at least one income item.", "error");
    return;
  }

  try {
    const formData = new FormData();
    formData.append("action", "save_income");
    formData.append("category", categoryName);
    formData.append("items", JSON.stringify(items));

    const response = await fetch("budget.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      // Reload data from server
      await loadUserData();

      // Update charts and table
      updateCharts();
      updateDataTable();

      // Reset form
      incomeForm.reset();
      incomeItems.innerHTML = "";
      addItemRow(incomeItems);
      incomeTotal.textContent = "$0.00";

      showNotification("Income category added successfully!", "success");
    } else {
      showNotification(data.message, "error");
    }
  } catch (error) {
    showNotification("Failed to save income data. Please try again.", "error");
    console.error("Income save error:", error);
  }
}

async function handleExpenseSubmit(e) {
  e.preventDefault();
  const categoryName = document.getElementById("expenseCategory").value;
  const items = [];

  expenseItems.querySelectorAll(".item-row").forEach((row) => {
    const name = row.querySelector(".item-name").value;
    const amount = row.querySelector(".item-amount").value;

    if (name && amount) {
      items.push({
        name: name,
        amount: parseFloat(amount),
        date: new Date().toISOString().split("T")[0],
      });
    }
  });

  if (items.length === 0) {
    showNotification("Please add at least one expense item.", "error");
    return;
  }

  try {
    const formData = new FormData();
    formData.append("action", "save_expense");
    formData.append("category", categoryName);
    formData.append("items", JSON.stringify(items));

    const response = await fetch("budget.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      // Reload data from server
      await loadUserData();

      // Update charts and table
      updateCharts();
      updateDataTable();

      // Reset form
      expenseForm.reset();
      expenseItems.innerHTML = "";
      addItemRow(expenseItems);
      expenseTotal.textContent = "$0.00";

      showNotification("Expense category added successfully!", "success");
    } else {
      showNotification(data.message, "error");
    }
  } catch (error) {
    showNotification("Failed to save expense data. Please try again.", "error");
    console.error("Expense save error:", error);
  }
}

async function loadUserData() {
  if (!budgetData.user) return;

  try {
    const response = await fetch("budget.php?action=get_budget_data");
    const data = await response.json();

    if (data.success) {
      console.log("Data: ",data);
      budgetData.income = data.income;
      budgetData.expenses = data.expenses;

      // Save to localStorage for offline access
      localStorage.setItem("budgetData", JSON.stringify(budgetData));
    } else {
      console.error("Failed to load user data:", data.message);
    }
  } catch (error) {
    console.error("Error loading user data:", error);

    // Try to load from localStorage as fallback
    const savedData = localStorage.getItem("budgetData");
    if (savedData) {
      const parsedData = JSON.parse(savedData);
      budgetData.income = parsedData.income || [];
      budgetData.expenses = parsedData.expenses || [];
    }
  }
}

// Data table functions
function updateDataTable() {
  // Clear the table
  dataTableBody.innerHTML = "";

  let totalIncome = 0;
  let totalExpenses = 0;

  // Add income items to the table
  budgetData.income.forEach((category) => {
    category.items.forEach((item) => {
      const row = document.createElement("tr");
      row.className = "income-row";
      row.innerHTML = `
        <td>Income</td>
        <td>${category.category}</td>
        <td>${item.name}</td>
        <td>$${item.amount.toFixed(2)}</td>
        <td>${item.date}</td>
        <td>
          <button class="icon-btn delete-item"
          data-category-id = "${item.category_id}"
           data-id="${
            item.id
          }" data-type="income">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `;
      dataTableBody.appendChild(row);
      totalIncome += item.amount;
    });
  });

  // Add expense items to the table
  budgetData.expenses.forEach((category) => {
    category.items.forEach((item) => {
      const row = document.createElement("tr");
      row.className = "expense-row";
      row.innerHTML = `
        <td>Expense</td>
        <td>${category.category}</td>
        <td>${item.name}</td>
        <td>$${item.amount.toFixed(2)}</td>
        <td>${item.date}</td>
        <td>
          <button class="icon-btn delete-item"
          data-category-id = "${item.category_id}"
          data-id="${
            item.id
          }" data-type="expense">
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
    balanceStatus.innerHTML =
      '<span class="balance-positive">Positive Balance</span>';
  } else if (net < 0) {
    balanceStatus.innerHTML =
      '<span class="balance-negative">Negative Balance</span>';
  } else {
    balanceStatus.innerHTML = "<span>Break Even</span>";
  }

  // If no data, show message
  if (dataTableBody.children.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td colspan="6" style="text-align: center;">No data available. Add income or expenses to see them here.</td>
    `;
    dataTableBody.appendChild(row);
  }
}

async function deleteBudgetItem(category_id, id, type) {
  try {
    const formData = new FormData();
    formData.append("action", "delete_item");
    formData.append("id", id);
    formData.append("type", type);

    const response = await fetch("budget.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      
      const button = document.querySelector(`.delete-item[data-id="${id}"][data-type="${type}"]`);
      if (button) button.closest("tr")?.remove();
      console.log(data,"FFF");

      // Remove from budgetData
      if (type === "expense") {
        budgetData.expenses.forEach(category => {
          category.items = category.items.filter(item => item.id != id);
        });
      } else if (type === "income") {
        budgetData.income.forEach(category => {
          category.items = category.items.filter(item => item.id != id);
        });
      }

      removeCategory(category_id, type);
      updateCharts();
      updateDataTable();

      showNotification("Item deleted successfully!", "success");
    } else {
      showNotification(data.message, "error");
    }
  } catch (error) {
    showNotification("Failed to delete item. Please try again.", "error");
    console.error("Delete error:", error);
  }
}

async function removeCategory(category_id, type){
  const formData = new FormData();
  formData.append("action", "delete_item_category");
  formData.append("category_id", category_id);
  formData.append("type", type)
  console.log(type, "type")
  const response = await fetch("budget.php", {
    method: "POST",
    body: formData,
  });
  console.log(type)
  const data = await response.json();
  if(data.success){
    updateCharts();
    updateDataTable();
  } else {
    showNotification("Error", data.message);
  }

}
// Chart functions
function initCharts() {
  const incomeCtx = document.getElementById("incomeChart").getContext("2d");
  const expenseCtx = document.getElementById("expenseChart").getContext("2d");

  incomeChart = new Chart(incomeCtx, {
    type: "pie",
    data: {
      labels: ["No data yet"],
      datasets: [
        {
          data: [1],
          backgroundColor: ["#4361ee"],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
        title: {
          display: true,
          text: "Income Distribution",
        },
      },
    },
  });

  expenseChart = new Chart(expenseCtx, {
    type: "pie",
    data: {
      labels: ["No data yet"],
      datasets: [
        {
          data: [1],
          backgroundColor: ["#f72585"],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
        title: {
          display: true,
          text: "Expense Distribution",
        },
      },
    },
  });
}

function updateCharts() {
  // Update income chart
  console.log(budgetData.income, "income")
  if (budgetData.income.length > 0) {
    const incomeLabels = budgetData.income.map((item) => item.category);
    const incomeData = budgetData.income.map((item) => item.total);
    const backgroundColors = generateColors(incomeData.length);

    incomeChart.data.labels = incomeLabels;
    incomeChart.data.datasets[0].data = incomeData;
    incomeChart.data.datasets[0].backgroundColor = backgroundColors;
  } else {
    incomeChart.data.labels = ["No data yet"];
    incomeChart.data.datasets[0].data = [1];
    incomeChart.data.datasets[0].backgroundColor = ["#4361ee"];
  }

  incomeChart.update();

  // Update expense chart
  if (budgetData.expenses.length > 0) {
    const expenseLabels = budgetData.expenses.map((item) => item.category);
    const expenseData = budgetData.expenses.map((item) => item.total);
    const backgroundColors = generateColors(expenseData.length);

    expenseChart.data.labels = expenseLabels;
    expenseChart.data.datasets[0].data = expenseData;
    expenseChart.data.datasets[0].backgroundColor = backgroundColors;
  } else {
    expenseChart.data.labels = ["No data yet"];
    expenseChart.data.datasets[0].data = [1];
    expenseChart.data.datasets[0].backgroundColor = ["#f72585"];
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
  alert(
    "PDF generation would be implemented with a library like jsPDF. This is a demo."
  );
  // In a real implementation, this would use jsPDF to create a PDF report
}

function exportToExcel() {
  if (budgetData.income.length === 0 && budgetData.expenses.length === 0) {
    showNotification("No data to export!", "error");
    return;
  }

  // Prepare data for Excel
  const wb = XLSX.utils.book_new();

  // Income data
  const incomeData = [["Category", "Item", "Amount", "Date"]];
  budgetData.income.forEach((category) => {
    category.items.forEach((item) => {
      incomeData.push([category.category, item.name, item.amount, item.date]);
    });
    incomeData.push([category.category, "TOTAL", category.total, ""]);
  });

  // Expense data
  const expenseData = [["Category", "Item", "Amount", "Date"]];
  budgetData.expenses.forEach((category) => {
    category.items.forEach((item) => {
      expenseData.push([category.category, item.name, item.amount, item.date]);
    });
    expenseData.push([category.category, "TOTAL", category.total, ""]);
  });

  // Summary data
  const summaryData = [
    ["Summary"],
    ["Total Income", calculateTotalIncome()],
    ["Total Expenses", calculateTotalExpenses()],
    ["Net Balance", calculateTotalIncome() - calculateTotalExpenses()],
  ];

  // Add sheets to workbook
  const incomeWS = XLSX.utils.aoa_to_sheet(incomeData);
  const expenseWS = XLSX.utils.aoa_to_sheet(expenseData);
  const summaryWS = XLSX.utils.aoa_to_sheet(summaryData);

  XLSX.utils.book_append_sheet(wb, incomeWS, "Income");
  XLSX.utils.book_append_sheet(wb, expenseWS, "Expenses");
  XLSX.utils.book_append_sheet(wb, summaryWS, "Summary");

  // Export to file
  XLSX.writeFile(wb, "budget_data.xlsx");
  showNotification("Data exported successfully!", "success");
}

function importFromExcel(e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    const data = new Uint8Array(e.target.result);
    const wb = XLSX.read(data, { type: "array" });

    // Reset current data
    budgetData.income = [];
    budgetData.expenses = [];

    // Process income sheet
    if (wb.SheetNames.includes("Income")) {
      const incomeWS = wb.Sheets["Income"];
      const incomeJson = XLSX.utils.sheet_to_json(incomeWS, { header: 1 });

      // Skip header row
      for (let i = 1; i < incomeJson.length; i++) {
        const row = incomeJson[i];
        if (row.length >= 3) {
          const category = row[0];
          const itemName = row[1];
          const amount = row[2];
          const date = row[3] || new Date().toISOString().split("T")[0];

          if (category && itemName && amount) {
            // Check if category exists
            let categoryObj = budgetData.income.find(
              (c) => c.category === category
            );
            if (!categoryObj) {
              categoryObj = {
                id: Date.now().toString(),
                category: category,
                items: [],
                total: 0,
              };
              budgetData.income.push(categoryObj);
            }

            if (itemName.toLowerCase() !== "total") {
              categoryObj.items.push({
                id:
                  Date.now().toString() +
                  Math.random().toString(36).substr(2, 5),
                name: itemName,
                amount: amount,
                date: date,
              });
              categoryObj.total += amount;
            }
          }
        }
      }
    }

    // Process expenses sheet
    if (wb.SheetNames.includes("Expenses")) {
      const expenseWS = wb.Sheets["Expenses"];
      const expenseJson = XLSX.utils.sheet_to_json(expenseWS, { header: 1 });

      // Skip header row
      for (let i = 1; i < expenseJson.length; i++) {
        const row = expenseJson[i];
        if (row.length >= 3) {
          const category = row[0];
          const itemName = row[1];
          const amount = row[2];
          const date = row[3] || new Date().toISOString().split("T")[0];

          if (category && itemName && amount) {
            // Check if category exists
            let categoryObj = budgetData.expenses.find(
              (c) => c.category === category
            );
            if (!categoryObj) {
              categoryObj = {
                id: Date.now().toString(),
                category: category,
                items: [],
                total: 0,
              };
              budgetData.expenses.push(categoryObj);
            }

            if (itemName.toLowerCase() !== "total") {
              categoryObj.items.push({
                id:
                  Date.now().toString() +
                  Math.random().toString(36).substr(2, 5),
                name: itemName,
                amount: amount,
                date: date,
              });
              categoryObj.total += amount;
            }
          }
        }
      }
    }

    // Save to localStorage if user is logged in
    if (budgetData.user) {
      localStorage.setItem(
        `budgetData_${budgetData.user.id}`,
        JSON.stringify({
          income: budgetData.income,
          expenses: budgetData.expenses,
        })
      );
    }
    localStorage.setItem("budgetData", JSON.stringify(budgetData));

    // Update charts and table
    updateCharts();
    updateDataTable();

    showNotification("Data imported successfully!", "success");
  };
  reader.readAsArrayBuffer(file);

  // Reset file input
  e.target.value = "";
}

// Helper functions
function calculateTotalIncome() {
  return budgetData.income.reduce(
    (total, category) => total + category.total,
    0
  );
}

function calculateTotalExpenses() {
  return budgetData.expenses.reduce(
    (total, category) => total + category.total,
    0
  );
}

function showNotification(message, type) {
  notification.textContent = message;
  notification.className = `notification ${type} show`;

  setTimeout(() => {
    notification.className = "notification";
  }, 3000);
}

function showHelp() {
  alert(
    "Budget Manager Pro Help:\n\n1. Add income and expense categories\n2. Add items to each category\n3. View your financial overview in the table\n4. See visual representations in the charts\n5. Export your data to Excel\n6. Register/login to save your data permanently"
  );
}

// Initialize the application
init();
