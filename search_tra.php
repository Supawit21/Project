<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['del_tra'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT tran_id,tran_name FROM transport
    WHERE status_tran = 1 AND tran_name LIKE '%$txt_search%'";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <tr>
        <td><?=$data['tran_name']?></td>
        <td><button type="button" class="btn btn-info cho" value="<?=$data['tran_id']?>">เลือกชื่อเส้นทาง</button></td>
    </tr>
<?php } ?>
<script>
    $(function() {
        $('.cho').on('click', function(e) {
            e.preventDefault();
            var tra = $(this).val();
            $.ajax({
                url: "de_tra.php",
                type: "post",
                data: {
                    txt: tra
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let tran_id   = response[x].tran_id;
                        let tran_name = response[x].tran_name;
                        /// นำตัวแปรไปใส่ ใน input ที่กำหนด ///
                        $('.tra_id').val(tran_id);
                        $('.tra_name').val(tran_name);
                    }
                }
            });
        });
    });
</script>