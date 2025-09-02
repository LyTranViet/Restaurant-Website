<?php
ob_start();
session_start();

$pageTitle = 'Users';

if(isset($_SESSION['username_restaurant_qRewacvAqzA']) && isset($_SESSION['password_restaurant_qRewacvAqzA']))
{
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';

    ?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            var vertical_menu = document.getElementById("vertical-menu");
            var current = vertical_menu.getElementsByClassName("active_link");
            if(current.length > 0){
                current[0].classList.remove("active_link");   
            }
            vertical_menu.getElementsByClassName('users_link')[0].className += " active_link";
        </script>
    <?php

    $do = '';
    if(isset($_GET['do']) && in_array(htmlspecialchars($_GET['do']), array('Add','Edit')))
        $do = $_GET['do'];
    else
        $do = 'Manage';

    /* ============ MANAGE USERS ============ */
    if($do == "Manage")
    {
        $stmt = $con->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll();
        ?>
            <div class="card">
                <div class="card-header">
                    <?php echo $pageTitle; ?>
                    <a href="users.php?do=Add" class="btn btn-primary btn-sm float-right">+ Add User</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered users-table">
                        <thead>
                            <tr>
                                <th scope="col">Username</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Role</th>
                                <th scope="col">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($users as $user)
                                {
                                    echo "<tr>";
                                        echo "<td>".$user['username']."</td>";
                                        echo "<td>".$user['email']."</td>";
                                        echo "<td>".$user['full_name']."</td>";
                                        echo "<td>".$user['role']."</td>";
                                        echo "<td>
                                                <a href='users.php?do=Edit&user_id=".$user['user_id']."' class='btn btn-success btn-sm'>
                                                    <i class='fa fa-edit'></i>
                                                </a>
                                              </td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>  
                </div>
            </div>
        <?php
    }

    /* ============ ADD USER ============ */
    elseif($do == 'Add')
    {
        ?>
        <div class="card">
            <div class="card-header">
                Add New User
            </div>
            <div class="card-body">
                <form method="POST" action="users.php?do=Add">
                    <div class="form-group">
                        <label for="user_name">Username</label>
                        <input type="text" class="form-control" name="user_name" required>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <input type="email" class="form-control" name="user_email" required>
                    </div>
                    <div class="form-group">
                        <label for="user_password">Password</label>
                        <input type="password" class="form-control" name="user_password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="employee" selected>Employee</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user_sbmt" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
        <?php

        if(isset($_POST['add_user_sbmt']))
        {
            $user_name     = test_input($_POST['user_name']);
            $user_fullname = test_input($_POST['full_name']);
            $user_email    = test_input($_POST['user_email']);
            $user_password = sha1($_POST['user_password']); // mã hoá đơn giản
            $role          = $_POST['role'];

            try {
                $stmt = $con->prepare("INSERT INTO users(username,email,full_name,password,role) VALUES(?,?,?,?,?)");
                $stmt->execute(array($user_name,$user_email,$user_fullname,$user_password,$role));
                ?>
                <script type="text/javascript">
                    swal("Add User","User has been added successfully", "success").then((value) => {
                        window.location.replace("users.php");
                    });
                </script>
                <?php
            } catch(Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
    }

    /* ============ EDIT USER ============ */
    elseif($do == 'Edit')
    {
        $user_id = (isset($_GET['user_id']) && is_numeric($_GET['user_id']))?intval($_GET['user_id']):0;
        if($user_id)
        {
            $stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute(array($user_id));
            $user = $stmt->fetch();
            $count = $stmt->rowCount();
            if($count > 0)
            {
                ?>
                <div class="card">
                    <div class="card-header">Edit User</div>
                    <div class="card-body">
                        <form method="POST" action="users.php?do=Edit&user_id=<?php echo $user['user_id'] ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id'];?>" >
                            <div class="form-group">
                                <label for="user_name">Username</label>
                                <input type="text" class="form-control" value="<?php echo $user['username'] ?>" name="user_name" required>
                            </div>
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" class="form-control" value="<?php echo $user['full_name'] ?>" name="full_name" required>
                            </div>
                            <div class="form-group">
                                <label for="user_email">Email</label>
                                <input type="email" class="form-control" value="<?php echo $user['email'] ?>" name="user_email" required>
                            </div>
                            <div class="form-group">
                                <label for="user_password">Change Password</label>
                                <input type="password" class="form-control" placeholder="Leave blank to keep old password" name="user_password">
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control" name="role" required>
                                    <option value="admin" <?php if($user['role']=="admin") echo "selected"; ?>>Admin</option>
                                    <option value="employee" <?php if($user['role']=="employee") echo "selected"; ?>>Employee</option>
                                </select>
                            </div>
                            <button type="submit" name="edit_user_sbmt" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
                <?php

                if(isset($_POST['edit_user_sbmt']))
                {
                    $user_id      = test_input($_POST['user_id']);
                    $user_name    = test_input($_POST['user_name']);
                    $user_fullname= test_input($_POST['full_name']);
                    $user_email   = test_input($_POST['user_email']);
                    $role         = $_POST['role'];
                    $user_password= $_POST['user_password'];

                    try {
                        if(empty($user_password)){
                            $stmt = $con->prepare("UPDATE users SET username=?, email=?, full_name=?, role=? WHERE user_id=?");
                            $stmt->execute(array($user_name,$user_email,$user_fullname,$role,$user_id));
                        } else {
                            $user_password = sha1($user_password);
                            $stmt = $con->prepare("UPDATE users SET username=?, email=?, full_name=?, password=?, role=? WHERE user_id=?");
                            $stmt->execute(array($user_name,$user_email,$user_fullname,$user_password,$role,$user_id));
                        }
                        ?>
                        <script type="text/javascript">
                            swal("Edit User","User has been updated successfully", "success").then((value) => {
                                window.location.replace("users.php");
                            });
                        </script>
                        <?php
                    } catch(Exception $e){
                        echo "Error: " . $e->getMessage();
                    }
                }
            }
        }
    }

    include 'Includes/templates/footer.php';
}
else
{
    header('Location: index.php');
    exit();
}
?>
