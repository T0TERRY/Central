<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_income') {
        // Save income data
        $category = $_POST['category'] ?? '';
        $items = json_decode($_POST['items'], true) ?? [];
        
        if (empty($category) || empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Category and items are required']);
            exit;
        }
        
        $conn = getDBConnection();
        
        try {
            $conn->beginTransaction();
            
            // Insert income category
            $stmt = $conn->prepare("INSERT INTO income_categories (user_id, category_name) VALUES (:user_id, :category_name)");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':category_name', $category);
            $stmt->execute();
            $categoryId = $conn->lastInsertId();
            
            // Insert income items
            $stmt = $conn->prepare("INSERT INTO income_items (category_id, item_name, amount, date) VALUES (:category_id, :item_name, :amount, :date)");
            
            foreach ($items as $item) {
                $stmt->bindParam(':category_id', $categoryId);
                $stmt->bindParam(':item_name', $item['name']);
                $stmt->bindParam(':amount', $item['amount']);
                $stmt->bindParam(':date', $item['date']);
                $stmt->execute();
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Income data saved successfully']);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to save income data: ' . $e->getMessage()]);
        }
        
        closeDBConnection($conn);
    }
    elseif ($action === 'save_expense') {
        // Save expense data
        $category = $_POST['category'] ?? '';
        $items = json_decode($_POST['items'], true) ?? [];
        
        if (empty($category) || empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Category and items are required']);
            exit;
        }
        
        $conn = getDBConnection();
        
        try {
            $conn->beginTransaction();
            
            // Insert expense category
            $stmt = $conn->prepare("INSERT INTO expense_categories (user_id, category_name) VALUES (:user_id, :category_name)");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':category_name', $category);
            $stmt->execute();
            $categoryId = $conn->lastInsertId();
            
            // Insert expense items
            $stmt = $conn->prepare("INSERT INTO expense_items (category_id, item_name, amount, date) VALUES (:category_id, :item_name, :amount, :date)");
            
            foreach ($items as $item) {
                $stmt->bindParam(':category_id', $categoryId);
                $stmt->bindParam(':item_name', $item['name']);
                $stmt->bindParam(':amount', $item['amount']);
                $stmt->bindParam(':date', $item['date']);
                $stmt->execute();
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Expense data saved successfully']);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to save expense data: ' . $e->getMessage()]);
        }
        
        closeDBConnection($conn);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get_budget_data') {
        // Get all budget data for the user
        $conn = getDBConnection();
        
        $incomeData = [];
        $expenseData = [];
        
        // Get income categories and items
        $stmt = $conn->prepare("
            SELECT ic.id, ic.category_name, ii.item_name, ii.amount, ii.date 
            FROM income_categories ic 
            LEFT JOIN income_items ii ON ic.id = ii.category_id 
            WHERE ic.user_id = :user_id
            ORDER BY ic.created_at DESC, ii.date DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoryId = $row['id'];
            if (!isset($incomeData[$categoryId])) {
                $incomeData[$categoryId] = [
                    'id' => $categoryId,
                    'category' => $row['category_name'],
                    'items' => [],
                    'total' => 0
                ];
            }
            
            if ($row['item_name']) {
                $incomeData[$categoryId]['items'][] = [
                    'id' => $row['id'],
                    'name' => $row['item_name'],
                    'amount' => (float)$row['amount'],
                    'date' => $row['date']
                ];
                $incomeData[$categoryId]['total'] += (float)$row['amount'];
            }
        }
        
        // Get expense categories and items
        $stmt = $conn->prepare("
            SELECT ec.id, ec.category_name, ei.item_name, ei.amount, ei.date 
            FROM expense_categories ec 
            LEFT JOIN expense_items ei ON ec.id = ei.category_id 
            WHERE ec.user_id = :user_id
            ORDER BY ec.created_at DESC, ei.date DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoryId = $row['id'];
            if (!isset($expenseData[$categoryId])) {
                $expenseData[$categoryId] = [
                    'id' => $categoryId,
                    'category' => $row['category_name'],
                    'items' => [],
                    'total' => 0
                ];
            }
            
            if ($row['item_name']) {
                $expenseData[$categoryId]['items'][] = [
                    'id' => $row['id'],
                    'name' => $row['item_name'],
                    'amount' => (float)$row['amount'],
                    'date' => $row['date']
                ];
                $expenseData[$categoryId]['total'] += (float)$row['amount'];
            }
        }
        
        closeDBConnection($conn);
        
        echo json_encode([
            'success' => true,
            'income' => array_values($incomeData),
            'expenses' => array_values($expenseData)
        ]);
    }
    elseif ($action === 'delete_item') {
        $id = $_POST['id'] ?? '';
        $type = $_POST['type'] ?? '';
        
        if (empty($id) || empty($type)) {
            echo json_encode(['success' => false, 'message' => 'ID and type are required']);
            exit;
        }
        
        $conn = getDBConnection();
        
        try {
            if ($type === 'income') {
                $stmt = $conn->prepare("DELETE FROM income_items WHERE id = :id");
            } elseif ($type === 'expense') {
                $stmt = $conn->prepare("DELETE FROM expense_items WHERE id = :id");
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid type']);
                exit;
            }
            
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to delete item: ' . $e->getMessage()]);
        }
        
        closeDBConnection($conn);
}
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>