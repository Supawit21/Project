<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['quo_pro'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT pro_id,pro_name,size_name,pro_amount FROM product 
    INNER JOIN product_size ON (pro_size=size_id) WHERE pro_name LIKE '%$txt_search%' AND status_pro = 1";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <tr>
        <td><?=$data['pro_name'] . ' ' . $data['size_name']?></td>
        <td><?=$data['pro_amount']?></td>
        <td><button type="button" class="btn btn-danger choose" value="<?=$data['pro_id']?>">เลือกสินค้า</button></td>
    </tr>
<?php } ?>
<script>
    $(function() {
        $('.choose').on('click', function(e) {
            e.preventDefault();
            var product1 = $(this).val();
            $.ajax({
                url: "search_order.php",
                type: "post",
                data: {
                    order: product1
                },
                success: function(data) {
                    $('.main').append(data);
                }
            });
        });
    });
</script>