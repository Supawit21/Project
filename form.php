<?php
session_start();
require('config/connect.php');
$db = new DB();
if (isset($_POST['btn_login'])) {
    $username    = mysqli_real_escape_string($db->conn, $_POST['emp_user']);
    $password    = mysqli_real_escape_string($db->conn, $_POST['emp_pass']);
    $txtSQL = "SELECT emp_id,emp_user,emp_pass,emp_nic,emp_name,pos_permit FROM employee INNER JOIN position ON (employee.emp_pos=position.pos_id) 
               WHERE emp_user='$username' AND emp_pass='$password'";
    $query_login = $db->query($txtSQL);
    if (mysqli_num_rows($query_login) > 0) {
        foreach($query_login as $value){
            $_SESSION['emp_name'] = $value['emp_name'];
            $_SESSION['pos_permit'] = $value['pos_permit'];
            $_SESSION['emp_nic'] = $value['emp_nic'];
            $_SESSION['emp_id'] = $value['emp_id'];
        }
        echo "<script type='text/javascript'>";
        echo "alert('เข้าสู่ระบบสำเร็จ');";
        echo "window.location = 'index.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เข้าสู่ระบบไม่สำเร็จ');";
        echo "window.location = 'form.php'; ";
        echo "</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kongkraisteel</title>
    <!-- CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<style>
    body{
        background-color: gray;
    }
</style>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto mt-5">
                <div class="card">
                    <form action="" method="post">
                        <div class="card-header text-center">
                            <h2>ลงชื่อเข้าใช้งาน</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <!-- <label for="emp_user" class="col-form-label">ชื่อผู้ใช้งาน</label> -->
                                <input type="text" class="form-control" id="emp_user" name="emp_user" autocomplete="off" placeholder="ชื่อผู้เข้าใช้งาน">
                            </div>
                            <div class="form-group">
                                <!-- <label for="emp_pass" class="col-form-label">รหัสผ่าน</label> -->
                                <input type="password" class="form-control" id="emp_pass" name="emp_pass" autocomplete="off" placeholder="รหัสผ่าน">
                            </div>
                        </div>
                        <div class="card-footer">
                            <input type="submit" name="btn_login" id="btn_login" class="btn btn-dark btn-lg btn-block" value="เข้าสู้ระบบ">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/script.php') ?>
</body>

</html>