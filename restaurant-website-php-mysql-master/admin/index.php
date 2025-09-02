<?php
session_start();
$pageTitle = 'Admin Login';

if (isset($_SESSION['username_restaurant_qRewacvAqzA']) && 
    isset($_SESSION['password_restaurant_qRewacvAqzA']) && 
    in_array($_SESSION['role_restaurant_qRewacvAqzA'], ['admin', 'employee'])) {
    header('Location: dashboard.php');
    die();
}

include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
?>

<div class="login">
    <form class="login-container validate-form" name="login-form" action="index.php" method="POST" onsubmit="return validateLoginForm()">
        <span class="login100-form-title p-b-32">Admin Login</span>
        
        <?php
        // Hiển thị thông báo lỗi nếu bị từ chối truy cập
        if (isset($_GET['error']) && $_GET['error'] == 'access_denied') {
            ?>
            <div class="alert alert-danger">
                <button data-dismiss="alert" class="close close-sm" type="button">
                    <span aria-hidden="true">×</span>
                </button>
                <div class="messages">
                    <div>You do not have permission to access this page!</div>
                </div>
            </div>
            <?php
        }

        // Xử lý đăng nhập
        if (isset($_POST['admin_login'])) {
            $username = test_input($_POST['username']);
            $password = test_input($_POST['password']);
            $hashedPass = sha1($password);

            $stmt = $con->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? AND password = ?");
            $stmt->execute(array($username, $hashedPass));
            $row = $stmt->fetch();
            $count = $stmt->rowCount();

            if ($count > 0) {
                if (in_array($row['role'], ['admin', 'employee'])) {
                    $_SESSION['username_restaurant_qRewacvAqzA'] = $username;
                    $_SESSION['password_restaurant_qRewacvAqzA'] = $password;
                    $_SESSION['userid_restaurant_qRewacvAqzA'] = $row['user_id'];
                    $_SESSION['role_restaurant_qRewacvAqzA'] = $row['role'];
                    session_regenerate_id(true); // Tăng bảo mật session
                    header('Location: dashboard.php');
                    die();
                } else {
                    ?>
                    <div class="alert alert-danger">
                        <button data-dismiss="alert" class="close close-sm" type="button">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="messages">
                            <div>Access denied! Only admins and employees can log in.</div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close close-sm" type="button">
                        <span aria-hidden="true">×</span>
                    </button>
                    <div class="messages">
                        <div>Username and/or password are incorrect!</div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

        <div class="form-input">
            <span class="txt1">Username</span>
            <input type="text" name="username" class="form-control username" oninput="document.getElementById('username_required').style.display = 'none'" id="user" autocomplete="off">
            <div class="invalid-feedback" id="username_required">Username is required!</div>
        </div>

        <div class="form-input">
            <span class="txt1">Password</span>
            <input type="password" name="password" class="form-control" oninput="document.getElementById('password_required').style.display = 'none'" id="password" autocomplete="new-password">
            <div class="invalid-feedback" id="password_required">Password is required!</div>
        </div>

        <p>
            <button type="submit" name="admin_login">Sign In</button>
        </p>

        <span class="forgotPW">Forgot your password? <a href="#">Reset it here.</a></span>
    </form>
</div>

<?php include 'Includes/templates/footer.php'; ?>