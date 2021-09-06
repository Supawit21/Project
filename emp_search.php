<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จากjquery search.js ///
$txtsearch = $_POST['query'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT employee.emp_id,emp_img,emp_title,emp_name,emp_surname,emp_email,emp_tel FROM employee
    INNER JOIN employee_tel ON (employee.emp_id=employee_tel.emp_id) WHERE emp_name LIKE '%$txtsearch%' OR emp_surname LIKE '%$txtsearch%' OR emp_tel LIKE '%$txtsearch%' AND status_emp = 1 GROUP BY employee.emp_id,emp_name";
    $result = $db->query($txtSQL);
}
?>
<?php foreach ($result as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['emp_id'] ?></th>
        <th style="text-align:center"><img src="image_emp/<?php echo $data['emp_img'] ?>" width="100px" height="100px" alt=""></th>
        <th style="text-align:center"><?php echo $data['emp_title'].' '.$data['emp_name'].' '.$data['emp_surname']?></th>
        <th style="text-align:center"><?php echo $data['emp_email'] ?></th>
        <th style="text-align:center"><?php echo $data['emp_tel'] ?></th>
        <th style="text-align:center"><a href="employee_edit.php?id=<?php echo $data['emp_id'] ?>" class="btn btn-secondary">แก้ไข</a>
            <a href="?emp_id=<?php echo $data['emp_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>