<?php
require('config/connect.php');
$db = new DB();
if(isset($_POST['function']) && $_POST['function'] == 'province'){
    $id = $_POST['id'];
    $txtSQL = "SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE ID_PROVINCE = '$id' ORDER BY AMPHUR_NAME";
    $result = $db->query($txtSQL);
    echo '<option selected disabled>กรุณาเลือกอำเภอ</option>';
    foreach($result as $value){
        echo '<option value="'.$value['AMPHUR_ID'].'">'.$value['AMPHUR_NAME'].'</option>';
    }
    exit();
}
if(isset($_POST['function']) && $_POST['function'] == 'amphur'){
    $id = $_POST['id'];
    $txtSQL = "SELECT DISTRICT_ID,DISTRICT_NAME FROM district WHERE ID_AMPHUR = '$id' ORDER BY DISTRICT_NAME";
    $result = $db->query($txtSQL);
    echo '<option selected disabled>กรุณาเลือกตำบล</option>';
    foreach($result as $value){
        echo '<option value="'.$value['DISTRICT_ID'].'">'.$value['DISTRICT_NAME'].'</option>';
    }
    exit();
}
if(isset($_POST['function']) && $_POST['function'] == 'district'){
    $id = $_POST['id'];
    $txtSQL = "SELECT ZIP_CODE FROM zipcode WHERE ID_DISTRICT = '$id'";
    $result = $db->query($txtSQL);
    $row = mysqli_fetch_array($result);
    echo $row['ZIP_CODE'];
    exit();
}
if(isset($_POST['function']) && $_POST['function'] == 'department'){
    $id = $_POST['id'];
    $txtSQL = "SELECT pos_id,pos_name FROM position WHERE pos_dep = '$id'";
    $result = $db->query($txtSQL);
    echo '<option selected disabled>กรุณาเลือกตำแหน่ง</option>';
    foreach($result as $value){
        echo '<option value="'.$value['pos_id'].'">'.$value['pos_name'].'</option>';
    }
    exit();
}
?>