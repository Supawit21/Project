<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['del_tru'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT tru_id,tru_reg,tru_type FROM trucks
    WHERE tru_type = '$txt_search'";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <tr>
        <td><?=$data['tru_reg']?></td>
        <td><?=$data['tru_type']?></td>
        <td><button type="button" class="btn btn-info cho" value="<?=$data['tru_id']?>">เลือกข้อมูลรถขนส่ง</button></td>
    </tr>
<?php } ?>
<script>
    $(function() {
        $('.cho').on('click', function(e) {
            e.preventDefault();
            var tru = $(this).val();
            $.ajax({
                url: "de_tru.php",
                type: "post",
                data: {
                    txt: tru
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let tru_id   = response[x].tru_id;
                        let tru_reg  = response[x].tru_reg;
                        let tru_type = response[x].tru_type;
                        /// นำตัวแปรไปใส่ ใน input ที่กำหนด ///
                        $('.tru_id').val(tru_id);
                        $('.tru_name').val(tru_reg+"/"+tru_type);
                    }
                }
            });
        });
    });
</script>