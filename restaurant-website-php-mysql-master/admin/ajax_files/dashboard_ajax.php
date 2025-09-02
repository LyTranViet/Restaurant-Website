<?php include '../connect.php'; ?>
<?php include '../Includes/functions/functions.php'; ?>

<?php
	
	// Kiểm tra nếu người dùng chưa đăng nhập hoặc vai trò không hợp lệ
if (!isset($_SESSION['role_restaurant_qRewacvAqzA']) || 
    !in_array($_SESSION['role_restaurant_qRewacvAqzA'], ['admin', 'employee'])) {
    echo json_encode(['error' => 'Access denied']);
    die();
}

$do_ = isset($_POST['do_']) ? $_POST['do_'] : '';

if ($do_ == 'Deliver_Order') {
    $order_id = $_POST['order_id'];
    $stmt = $con->prepare("UPDATE placed_orders SET delivered = 1 WHERE order_id = ?");
    $stmt->execute(array($order_id));
    echo json_encode(['success' => 'Order delivered']);
} elseif ($do_ == 'Cancel_Order') {
    $order_id = $_POST['order_id'];
    $cancellation_reason = $_POST['cancellation_reason_order'];
    $stmt = $con->prepare("UPDATE placed_orders SET canceled = 1, cancellation_reason = ? WHERE order_id = ?");
    $stmt->execute(array($cancellation_reason, $order_id));
    echo json_encode(['success' => 'Order canceled']);
} elseif ($do_ == 'Liberate_Table') {
    $reservation_id = $_POST['reservation_id'];
    $stmt = $con->prepare("UPDATE reservations SET canceled = 0, selected_time = NULL WHERE reservation_id = ?");
    $stmt->execute(array($reservation_id));
    echo json_encode(['success' => 'Table liberated']);
} elseif ($do_ == 'Cancel_Reservation') {
    $reservation_id = $_POST['reservation_id'];
    $cancellation_reason = $_POST['cancellation_reason_reservation'];
    $stmt = $con->prepare("UPDATE reservations SET canceled = 1, cancellation_reason = ? WHERE reservation_id = ?");
    $stmt->execute(array($cancellation_reason, $reservation_id));
    echo json_encode(['success' => 'Reservation canceled']);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>
