<?php
require('config/connect.php');
$db = new DB();
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('KKS',NVL(lpad(substr(max(emp_id),4,4)+1,4,'0'),'0001')) as emp FROM employee";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลพนักงาน ///
if (isset($_POST['emp_insert'])) {
    $emp_id      = $fetch_id['emp'];
    $emp_title   = $_POST['emp_title'];
    $emp_name    = $_POST['emp_name'];
    $emp_surname = $_POST['emp_surname'];
    $emp_nic     = $_POST['emp_nic'];
    $emp_nation  = $_POST['emp_nat'];
    $emp_bdate   = $_POST['emp_bdate'];
    $emp_iden    = $_POST['emp_iden'];
    $emp_email   = $_POST['emp_email'];
    $emp_gen     = $_POST['emp_gen'];
    $emp_add     = $_POST['emp_add'];
    $emp_zip     = $_POST['zipcode'];
    $id_dis      = $_POST['district'];
    $emp_pos     = $_POST['emp_pos'];
    $emp_user    = $_POST['emp_user'];
    $emp_pass    = $_POST['emp_pass'];
    $emp_tel     = $_POST['emp_tel'];
    /// เก็บชื่อไฟล์ ///
    $imgFile = $_FILES['emp_img']['name'];
    $tmp = $_FILES['emp_img']['tmp_name'];
    /// เก็บ path ไฟล์ ///
    $folder = "image_emp/";
    /// บันทึกรูปเข้าไฟล์ ///
    move_uploaded_file($tmp,$folder.$imgFile);
    $arr = array(
         "emp_id" => $emp_id,
         "emp_img" => $imgFile,
         "emp_title" => $emp_title,
         "emp_name" => $emp_name,
         "emp_surname" => $emp_surname,
         "emp_nic" => $emp_nic,
         "emp_bdate" => $emp_bdate,
         "emp_iden" => $emp_iden,
         "emp_nation" => $emp_nation,
         "emp_email" => $emp_email,
         "emp_gen" => $emp_gen,
         "emp_add" => $emp_add,
         "emp_zip" => $emp_zip,
         "id_dis" => $id_dis,
         "emp_pos" => $emp_pos,
         "emp_user" => $emp_user,
         "emp_pass" => $emp_pass
    );
    $insert = $db->insert("employee", $arr);
    foreach ($emp_tel as  $tel) {
        $arr1 = array(
            "emp_id" => $emp_id,
            "emp_tel" => $tel
        );
        $insert_all = $db->insert("employee_tel", $arr1);
        if ($insert_all) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลสำเร็จ');";
            echo "window.location = 'employee.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
            echo "</script>";
        }
    }
}
