<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['quo_cus'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT cus_id,cus_name FROM customer
    WHERE cus_name LIKE '%$txt_search%' AND status_cus = 1";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <tr>
        <td><?=$data['cus_id']?></td>
        <td><?=$data['cus_name']?></td>
        <td><button type="button" class="btn btn-info cho" value="<?=$data['cus_id']?>">เลือกข้อมูลลูกค้า</button></td>
    </tr>
<?php } ?>
<script>
    $(function() {
        $('.cho').on('click', function(e) {
            e.preventDefault();
            var cus = $(this).val();
            $.ajax({
                url: "quo_cus.php",
                type: "post",
                data: {
                    txt: cus
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let cus_id   = response[x].cus_id;
                        let cus_name = response[x].cus_name;
                        /// นำตัวแปรไปใส่ ใน input ที่กำหนด ///
                        $('.txt_id').val(cus_id);
                        $('.txt_name').val(cus_name);
                    }
                }
            });
        });
    });
</script>