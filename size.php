<?php
require('config/connect.php');
$db = new DB();
if(isset($_POST['function']) && $_POST['function'] == 'type'){
    $id = $_POST['id'];
    $txtSQL = "SELECT size_id,size_name FROM product_size WHERE size_type = '$id'";
    $result_size = $db->query($txtSQL);
    echo '<option selected disabled>เลือกขนาดสินค้า</option>';
    foreach($result_size  as $value){
        echo '<option value="'.$value['size_id'].'">'.$value['size_name'].'</option>';
    }
    exit();
}
?>