<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn,$_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if(!empty($txtsearch)){
$txtSQL =  "SELECT pro_id,pro_img,pro_name,size_name,pro_price,pro_amount FROM product 
            INNER JOIN product_size ON (product.pro_size=product_size.size_id) WHERE pro_name LIKE '%$txtsearch%' AND status_pro = 1";
$result_search = $db->query($txtSQL);
}
?>
<?php foreach ($result_search as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['pro_id'] ?></th>
        <th style="text-align:center"><img src="image_pro/<?php echo $data['pro_img']?>" alt="" width="100px" height="100px"></th>
        <th style="text-align:center"><?php echo $data['pro_name'].''.$data['size_name']?></th>
        <th style="text-align:center"><?php echo $data['pro_amount'] ?></th>
        <th style="text-align:center"><?php echo number_format($data['pro_price']);?></th>
        <th style="text-align:center">
            <a href="product_edit.php?id=<?php echo $data['pro_id'] ?>" class="btn btn-secondary">แก้ไข</a>
            <a href="?pro_id=<?php echo $data['pro_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>