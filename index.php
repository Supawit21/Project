<?php
session_start();
require('config/connect.php');
$db = new DB();
if(!isset($_SESSION['emp_name'])){
    header('location:form.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php') ?>
<body>
    <?php include('includes/sidebar.php') ?>
    <main>
        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-md-10 ml-auto">
                    <div class="card border-0 mt-5">
                        <div class="card-body">
                            <h2>หน้าแรก</h2>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
</body>
</html>
