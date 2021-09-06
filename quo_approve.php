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
$txtSQL = "SELECT pro_id,pro_name,size_name,quo.quol_order,quo.quol_cnt,quo.quol_cnte,unit_name,quoc_cost,quoc_coste,count,status_promo  FROM product
INNER JOIN product_size   ON (pro_size=size_id)
INNER JOIN convent        ON (pro_id=con_product)
INNER JOIN product_unit   ON (con_unts=unit_id)
INNER JOIN quotation_cost ON (pro_id=quoc_pro)
INNER JOIN quotation_list as quo ON (quoc_id=quo.quol_id)
INNER JOIN quotation_list ON (quoc_order=quo.quol_order)
LEFT JOIN list_promotion ON (pro_id=list_promo)
LEFT JOIN promotion      ON (list_id=promo_id)
WHERE quo.quol_id = '$id'
GROUP BY pro_id";
$rs_context = $db->query($txtSQL);
/// บันทึกใบรับสินค้า ///
if (isset($_POST['quo_yes'])) {
    $quo_tol    = $_POST['app_all'];
    $quol_sum   = $_POST['app_sum'];
    $quo_id     = $_POST['quo_id'];
    $quol_order = $_POST['ord'];
    $chk_pro    = $_POST['chk_one']; 
    /// update total ///
    $arr = array(
        "quo_tol" => $quo_tol
    );
    $where_condition = array(
        "quo_id" => $quo_id
    );
    $update = $db->update("quotation",$arr,$where_condition);
    /// update sum ///
    foreach($quol_sum as $key => $data){
        $arr1 = array(
            "quol_sum" => $data
        );
        $where_condition1 = array(
            "quol_id"    => $quo_id,
            "quol_order" => $quol_order[$key]
        );
        $update1 = $db->update("quotation_list",$arr1,$where_condition1);
    }
    /// update status product ///
    foreach($chk_pro as $data){
        $arr2 = array(
            "quoc_status" => 1
        );
        $where_condition2 = array(
            "quoc_id" => $quo_id,
            "quoc_pro" => $data
        );
        $update2 = $db->update("quotation_cost",$arr2,$where_condition2);
    }
    /// update head quotation status ///
    $arr3 = array(
        "quo_status" => 1
    );
    $where_condition3 = array(
        "quo_id" => $quo_id
    );
    $update3 = $db->update("quotation",$arr3,$where_condition3);
    if($update3){
        echo "<script type='text/javascript'>";
        echo "alert('อนุมัติใบเสนอราคาสำเร็จ');";
        echo "window.location = 'quo_list.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('อนุมัติใบเสนอราคาไม่สำเร็จ');";
        echo "</script>";
    }
}else if(isset($_POST['quo_no'])){
    $quo_id = $_POST['quo_id'];
    $pro_id = $_POST['stc'];
    $order  = $_POST['app_cnt'];
    $arr_no = array(
        "quo_status" => 2
    );
    $where_condition = array(
        "quo_id" => $quo_id
    );
    $update_no = $db->update("quotation", $arr_no, $where_condition);
    /// คืนสต็อก ///
    foreach($pro_id as $key => $data){
        $txtSQL = "SELECT quo_pro FROM product WHERE pro_id='$data'";
        $rs_stock = $db->query($txtSQL);
        /// จำนวนสินค้า ///
        $amount = $order[$key];
        /// สินค้า ///
        $p = $data;
        foreach($rs_stock as $data){
            $cnt = $data['quo_pro'];
            $arr_p = array(
                "quo_pro" => $cnt - $amount
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
                            <h2>อนุมัติใบเสนอราคา</h2>
                        </div>
                    </div>
                    <form name="quo_approve" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group ml-2">
                                        <label>ชื่อลูกค้า</label>
                                        <input type="text" name="cus_name" class="form-control" value="<?= $fetch_head['cus_name'] ?>" readonly>
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
                                            <th><input type="checkbox" name="chk_all" class="form-check-input" id="check_all">รายการสินค้า
                                            </th>
                                            <th scope="col" width="15%">จำนวนเสนอ</th>
                                            <th scope="col" width="15%">ราคา/หน่วย</th>
                                            <th scope="col" width="15%">ส่วนลด</th>
                                            <th scope="col" width="15%">ราคารวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($rs_context as $data) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="chk_one[]"  class="form-check-input check_pro" value="<?=$data['pro_id']?>"><?= $data['pro_name'] . ' ' . $data['size_name'] ?>
                                                    <input type="hidden" name="stc[]" value="<?=$data['pro_id']?>">
                                                </td>
                                                <td>
                                                    <?php if($data['quol_cnte'] == "") { ?>
                                                        <?=$data['quol_cnt'].' '.$data['unit_name']?>
                                                        <input type="hidden" name="app_cnt[]" class="form-control app_cnt" value="<?=$data['quol_cnt']?>">
                                                        <input type="hidden" name="ord[]" class="form-control app_cnt" value="<?=$data['quol_order']?>">
                                                    <?php } else { ?>
                                                        <?=$data['quol_cnte'].' '.$data['unit_name']?>
                                                        <input type="hidden" name="app_cnt[]" class="form-control app_cnt" value="<?=$data['quol_cnte']?>">
                                                        <input type="hidden" name="ord[]" class="form-control app_cnt" value="<?=$data['quol_order']?>">
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if($data['quoc_coste'] == "") { ?>
                                                        <?=number_format($data['quoc_cost']).' '.'บาท'?>
                                                        <input type="hidden" name="app_cost" class="form-control app_cost" value="<?=$data['quoc_cost']?>">
                                                    <?php } else { ?>
                                                        <?=number_format($data['quoc_coste']).' '.'บาท'?>
                                                        <input type="hidden" name="app_cost" class="form-control app_cost" value="<?=$data['quoc_coste']?>">
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
                                                    <span class="txt_sum"></span>
                                                        <div class="tol">
                                                            <input type="hidden" name="app_sum[]" class="form-control text-center app_sum" value="0">
                                                        </div>
                                                </td>
                                            </tr>
                                    </tbody>
                                <?php } ?>
                                </table>
                                <div class="form-row">
                                    <div class="form-group">
                                        <input type="submit" name="quo_yes" class="btn btn-primary" value="อนุมัติใบเสนอราคา">
                                        <input type="submit" name="quo_no"  class="btn btn-danger" value="ไม่อนุมัติใบเสนอราคา">
                                    </div>
                                    <table class="table table-bordered ml-auto" style="width: 40%;">
                                        <tr>
                                            <td colspan="2" style="text-align: right;"><strong>ราคารวมสินค้า</strong></td>
                                            <td style="text-align: center;"><span class="txt_edit1"></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: right;"><strong>ภาษีมูลค่าเพิ่ม (7%)</strong></td>
                                            <td style="text-align: center;"><span class="txt_edit2"></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: right;"><strong>ราคารวมสินค้าทั้งหมด</strong></td>
                                            <td style="text-align: center;">
                                                <span class="txt_edit3"></span>
                                                <input type="hidden" name="app_all" class="app_all form-control">
                                            </td>
                                        </tr>
                                    </table>
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
            $('#check_all').on('change',function(){
                /// checkbox ถูกติ้กทั้งหมด ///
                $('input:checkbox').prop('checked', this.checked);
                /// คำนวณ ///
                var total = 0;
                $('input.check_pro:checked').each(function(){
                var cnt     = parseFloat($(this).parents('tr').find('input.app_cnt').val());
                var cost    = parseFloat($(this).parents('tr').find('input.app_cost').val());
                var percent = parseFloat($(this).parents('tr').find('input.percent').val());
                /// check โปรโมชั่น ///
                if(!percent){
                    parseFloat($(this).parents('tr').find('.txt_sum').text(Number(cnt * cost).toLocaleString('en')+' '+'บาท'));
                    parseFloat($(this).parents('tr').find('input.app_sum').val(cnt * cost));
                }else{
                    parseFloat($(this).parents('tr').find('.txt_sum').text(Number((cnt * cost)-((cnt * cost * percent)/100)).toLocaleString('en')+' '+'บาท'));
                    parseFloat($(this).parents('tr').find('input.app_sum').val((cnt * cost)-((cnt * cost * percent)/100)));
                }
                /// คำนวณราคารวม ///
                var value = parseFloat($(this).parents('tr').find('input.app_sum').val());
                total = total + value;
                });
                /// ผลลัพท์ ทั้งหมด ///
                $('.txt_edit1').text(Number(total).toLocaleString('en')+' '+'บาท');
                $('.txt_edit2').text(Number(total*0.07).toLocaleString('en')+' '+'บาท');
                $('.txt_edit3').text(Number(total+(total*0.07)).toLocaleString('en')+' '+'บาท');
                $('.app_all').val(total+(total*0.07));
                /// uncheck ทั้งหมด ///
                $('input.check_pro:not(:checked)').each(function(){
                    parseFloat($(this).parents('tr').find('.txt_sum').text('0 บาท'));
                    parseFloat($(this).parents('tr').find('input.app_sum').val(0));
                });
            });
            $('input.check_pro').on('change',function(){
                var par      = $(this).closest('tr');
                var cnt      = par.find('input.app_cnt').val();
                var cost     = par.find('input.app_cost').val();
                var percent  = par.find('input.percent').val();
                /// เช็คส่วนลด ///
                if(!percent){
                    par.find('.txt_sum').text(Number(cnt * cost).toLocaleString('en')+' '+'บาท');
                    par.find('input.app_sum').val(cnt * cost);
                }else{
                    par.find('.txt_sum').text(Number((cnt * cost)-((cnt * cost * percent)/100)).toLocaleString('en')+' '+'บาท');
                    par.find('input.app_sum').val((cnt * cost)-((cnt * cost * percent)/100));
                }
                /// เช็คค่า checkbox ///
                var total = 0;
                $('input.check_pro:checked').each(function(){
                    var value = parseFloat($(this).parents('tr').find('input.app_sum').val());
                    total = total + value;
                });
                $('.txt_edit1').text(Number(total).toLocaleString('en')+' '+'บาท');
                $('.txt_edit2').text(Number(total*0.07).toLocaleString('en')+' '+'บาท');
                $('.txt_edit3').text(Number(total+(total*0.07)).toLocaleString('en')+' '+'บาท');
                $('.app_all').val(total+(total*0.07));
                if(!this.checked){
                    par.find('.txt_sum').text('0 บาท');
                    par.find('input.app_sum').val(0);
                }
            });
        });
    </script>
</body>

</html>