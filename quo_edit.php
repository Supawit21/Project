<?php
session_start();
require('config/connect.php');
$db = new DB();
/// รับค่าจากหน้าแสดงข้อมูลใบเสนอราคา ///
$id = $_GET['quo_id'];
/// ดึงข้อมูลหัวใบเสนอราคา ///
$txtSQL = "SELECT quo_id,quo_start,quo_end,cus_name,emp_name FROM quotation
INNER JOIN customer ON (quo_cus=cus_id)
INNER JOIN employee ON (quo_emp=emp_id)
WHERE quo_id = '$id'";
$rs_head = $db->query($txtSQL);
$fetch_head = mysqli_fetch_assoc($rs_head);
/// ดึงรายการใบเสนอราคา ///
$txtSQL = "SELECT pro_id,pro_name,size_name,pro_size,quol_order,quol_cnt,quol_cnte,unit_name,qc1.quoc_cost,qc1.quoc_coste,qc1.quoc_order,count,status_promo FROM quotation_list
INNER JOIN quotation_cost as qc1 ON (quol_id=qc1.quoc_id)
INNER JOIN quotation_cost ON (quol_order=qc1.quoc_order)
INNER JOIN product ON (qc1.quoc_pro=pro_id)
INNER JOIN product_size ON (pro_size=size_id)
INNER JOIN convent ON (pro_id=con_product)
INNER JOIN product_unit ON (con_unts=unit_id)
LEFT JOIN list_promotion ON (pro_id=list_promo)
LEFT JOIN promotion ON (list_id=promo_id)
WHERE qc1.quoc_id = '$id'
GROUP BY qc1.quoc_pro";
$rs_context = $db->query($txtSQL);
/// บันทึกใบรับสินค้า ///
if (isset($_POST['quo_edit'])) {
    /// update quotation ///
    $pro_edit   = $_POST['pro_edit'];
    $quol_cnte  = $_POST['cnt_edit'];
    $quoc_coste = $_POST['cost_edit'];
    $quol_order  = $_POST['ord'];
    $quo_id = $_POST['quo_id'];
    $sts = $_POST['chk_no'];
    $quoc_order = $_POST['cost_order'];
    $cnt_old = $_POST['cnt_old'];
    /// new product ///
    $pro_id   = $_POST['pro_id'];
    $quol_cnt = $_POST['cnt'];
    $quoc_cost = $_POST['quo_cost'];
    /// update cnt_edit ///
    foreach($cnt_old as $key => $data){
        $arr = array(
            "quol_cnte" => $data
        );
        $where_condition = array(
            "quol_order" => $quol_order[$key],
            "quol_id"    => $quo_id
        );
        $update = $db->update("quotation_list",$arr,$where_condition);
    }
    /// update cost_edit ///
    foreach($quoc_coste as $key => $data){
        $arr_e = array(
            "quoc_coste" => $data
        );
        $where_condition_e = array(
            "quoc_order" => $quoc_order[$key],
            "quoc_id"    => $quo_id
        );
        $update_e = $db->update("quotation_cost",$arr_e,$where_condition_e);
    }
    /// chk status ///
    foreach($sts as $key => $data){
        $arr_up = array(
            "quoc_status" => 2
        );
        $where_condition_up = array(
            "quoc_id"  => $quo_id,
            "quoc_pro" => $data
        );
        $update_up = $db->update("quotation_cost",$arr_up,$where_condition_up);
    }
    /// insert new quotation_list ///
    $txtSQL  = "SELECT MAX(quol_order) as ord1 FROM quotation_list WHERE quol_id = '$quo_id'";
    $rs_ord  = $db->query($txtSQL);
    $ft      = mysqli_fetch_assoc($rs_ord);
    foreach($quol_cnt as $key => $data){
        $arr_new = array(
            "quol_order" => ++$ft['ord1'],
            "quol_id"    => $quo_id,
            "quol_cnt"   => $data
        );
        $insert_new = $db->insert("quotation_list",$arr_new);
    }
    /// insert new quotation_cost ///
    $txtSQL  = "SELECT MAX(quoc_order) as ord2 FROM quotation_cost WHERE quoc_id = '$quo_id'";
    $rs_ord  = $db->query($txtSQL);
    $ft      = mysqli_fetch_assoc($rs_ord);
    foreach($pro_id as $key => $data){
        $arr_new1 = array(
            "quoc_order"   => ++$ft['ord2'],
            "quoc_id"      => $quo_id,
            "quoc_product" => $data,
            "quoc_cost"    => $quoc_cost[$key],
            "quoc_status"  => 0
        );
        $insert_new1 = $db->insert("quotation_cost",$arr_new1);
    }
    /// ตัดสต็อกสินค้ารายการใหม่ ///
    if($quol_cnt){
        foreach($quoc_pro as $key => $data){
            $txtSQL = "SELECT quo_pro FROM product WHERE pro_id='$data'";
            $rs_stock = $db->query($txtSQL);
            /// จำนวนสินค้า ///
            $amount  = $quol_cnt[$key];
            /// สินค้า ///
            $p = $data;
            foreach($rs_stock as $data){
                $cnt = $data['quo_pro'];
                $arr_p = array(
                    "quo_pro" => $cnt + $amount
                );
                $where_condition = array(
                    "pro_id" => $p
                );
                $update = $db->update("product",$arr_p,$where_condition);
            }
        }
    }
    /// ตัดสต้อกสินค้ารายการที่แก้ไข ///
    if($cnt_old){
        foreach ($pro_edit as $key => $data) {          
            $txtSQL = "SELECT quo_pro FROM product WHERE pro_id='$data'";
            $rs_stock = $db->query($txtSQL);
            /// จำนวนที่เสนอราคา ///
            $amount1  = $cnt_old[$key];
            $amount = $quol_cnte[$key];
            /// สินค้า ///
            $p = $data;
            foreach($rs_stock as $data){
                $cnt = $data['quo_pro'];
                $arr_p = array(
                    "quo_pro" => $cnt - $amount + $amount1
                );
                $where_condition = array(
                    "pro_id" => $p
                );
                $update = $db->update("product",$arr_p,$where_condition);
                if ($update) {
                    echo "<script type='text/javascript'>";
                    echo "alert('เพิ่มใบเสนอราคาสำเร็จ');";
                    echo "window.location = 'quo_list.php'; ";
                    echo "</script>";
                } else {
                    echo "<script type='text/javascript'>";
                    echo "alert('เพิ่มใบเสนอราคาไม่สำเร็จ');";
                    echo "</script>";
                }
            }
        }
    }
    /// เปลี่ยน status เป็น 0 เพื่ออนุมัติใบเสนอราคา ///
    $arr_ap = array(
        "quo_status" => 0
    );
    $where_condition_ap = array(
        "quo_id" => $quo_id
    );
    $update_ap = $db->update("quotation",$arr_ap,$where_condition_ap);
    if ($update_ap) {
        echo "<script type='text/javascript'>";
        echo "alert('บันทึกใบเสนอราคาสำเร็จ');";
        echo "window.location = 'quo_list.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('บันทึกใบเสนอราคาไม่สำเร็จ');";
        echo "</script>";
    }
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
                        <div class="card-body d-sm-flex justify-content-between">
                            <h2>ใบเสนอราคา(แก้ไข)</h2>
                        </div>
                    </div>
                    <form name="quo_edit" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group ml-2">
                                        <label>ชื่อลูกค้า</label>
                                        <input type="text" name="cus_name" class="form-control" value="<?= $fetch_head['cus_name'] ?>" readonly>
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong1">เลือกสินค้าที่จะเสนอราคา</button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong1<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLongTitle">ข้อมูลสินค้า</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="info_lot">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบเสนอราคา</label>
                                        <input type="text" name="quo_id" class="form-control" value="<?= $fetch_head['quo_id'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่เสนอราคา</label>
                                        <input type="date" name="quo_start" class="form-control" value="<?= $fetch_head['quo_start'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อผู้รับสินค้า</label>
                                        <input type="text" name="emp_name" class="form-control" value="<?= $fetch_head['emp_name'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่ครบกำหนด</label>
                                        <input type="date" name="quo_end" class="form-control" value="<?= $fetch_head['quo_end'] ?>" readonly>
                                    </div>
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>รายการสินค้า</th>
                                            <th scope="col" width="16%">จำนวนเสนอที่เดิม</th>
                                            <th scope="col" width="16%">จำนวนเสนอที่แก้ไข</th>
                                            <th scope="col" width="16%">ราคา/หน่วยที่แก้ไข</th>
                                            <th scope="col" width="16%">ส่วนลด</th>
                                            <th><input type="checkbox" name="chk_all" class="form-check-input" id="check_all">เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <tbody class="main">
                                        <?php
                                        // $num = 1;
                                        foreach ($rs_context as $data) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <?= $data['pro_name'] . ' ' . $data['size_name'] ?>
                                                    <input type="hidden" name="pro_edit[]" class="form-control pro_tx" value="<?=$data['pro_id']?>">
                                                    <input type="hidden" name="ord[]" class="form-control ord" value="<?=$data['quol_order']?>">
                                                </td>
                                                <td>
                                                    <?php if($data['quol_cnte'] == "") { ?>
                                                    <div class="input-group">
                                                        <input type="text" name="cnt_edit[]" class="form-control text-center num quo_cnt" value="<?= $data['quol_cnt'] ?>" aria-describedby="basic-addon4">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon4"><?= $data['unit_name'] ?></span>
                                                        </div>
                                                    </div>
                                                    <?php } else {?>
                                                    <div class="input-group">
                                                        <input type="text" name="cnt_edit[]" class="form-control text-center num quo_cnt" value="<?= $data['quol_cnte'] ?>" aria-describedby="basic-addon4">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon4"><?= $data['unit_name'] ?></span>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="cnt_old[]" class="form-control text-center num" aria-describedby="basic-addon4">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon4"><?= $data['unit_name'] ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if($data['quoc_coste'] == "") { ?>
                                                    <div class="input-group">
                                                        <input type="text" name="cost_edit[]" class="form-control text-center num quo_cost" value="<?= $data['quoc_cost'] ?>" aria-describedby="basic-addon4">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon4">บาท</span>
                                                        </div>
                                                        <input type="hidden" name="cost_order[]" value="<?=$data['quoc_order']?>">
                                                    </div>
                                                    <?php } else {?>
                                                    <div class="input-group">
                                                        <input type="text" name="cost_edit[]" class="form-control text-center num quo_cost" value="<?= $data['quoc_cost'] ?>" aria-describedby="basic-addon4">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon4">บาท</span>
                                                        </div>
                                                        <input type="hidden" name="cost_order[]" value="<?=$data['quoc_order']?>">
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($data['status_promo'] == "" || $data['status_promo'] == "0") { ?>
                                                        <input type="hidden" name="cost" autocomplete="off" class="form-control text-center percent" value="" readonly>
                                                    <?php } else { ?>
                                                        <?= $data['count'] . ' ' . '%' ?>
                                                        <input type="hidden" name="cost" autocomplete="off" class="form-control text-center percent" value="<?= $data['count'] ?>" readonly>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="chk ml-4">
                                                        <input class="form-check-input chk_no" type="checkbox" value="<?=$data['pro_id']?>" id="defaultCheck1" name="chk_no[]">
                                                    </div>
                                                </td>
                                            </tr>
                                    </tbody>
                                <?php } ?>
                                </table>
                                <div class="form-row">
                                    <div class="form-group">
                                        <input type="submit" name="quo_edit" class="btn btn-primary" value="บันทึกใบเสนอราคา">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
    <script>
        $(function() {
            function loadData1(page) {
                $.ajax({
                    url: "pagination_lot.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#info_lot").html(data);
                    }
                });
            }
            loadData1();
            $(document).on("click", ".pagination li a", function(e) {
                e.preventDefault();
                var pageId = $(this).attr("id");
                loadData1(pageId);
            });
            $('.chk_no').on('change',function(){
                let par       = $(this).closest('tr');
                let quo_count = par.find('input.quo_cnt');
                let quo_cost  = par.find('input.quo_cost');
                let product   = par.find('input.pro_tx');
                let ord       = par.find('input.ord');
                if(this.checked){
                    quo_count.prop('disabled',true);
                    quo_cost.prop('disabled',true);
                    product.prop('disabled',true);
                    ord.prop('disabled',true);
                }else{
                    quo_count.prop('disabled',false);
                    quo_cost.prop('disabled',false);
                    product.prop('disabled',false);
                    ord.prop('disabled',false);
                }
            });
            $('#check_all').on('change',function(){
                /// checkbox ถูกติ้กทั้งหมด ///
                $('input:checkbox').prop('checked', this.checked);
            });
        });
    </script>
</body>

</html>