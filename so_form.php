<?php
session_start();
require('config/connect.php');
$db = new DB();
$txt_nic = $_SESSION['emp_nic'];
$txt_id  = $_SESSION['emp_id'];
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('SO',NVL(lpad(substr(max(so_id),4)+1,6,'0'),'000001')) as so FROM sales_order";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_assoc($result);
/// select รหัสใบเสนอ/ชื่อลูกค้า ///
$txtSQL = "SELECT quo_id,cus_name FROM quotation
INNER JOIN customer ON (quo_cus=cus_id)
WHERE quo_status = 1 AND NOT EXISTS (SELECT id_quo FROM sales_order_list WHERE quo_id=id_quo)";
$rs_so = $db->query($txtSQL);
/// บันทึกใบรับสินค้า ///
if (isset($_POST['so_insert'])) {
    /// insert sales_order ///
    $so_id    = $_POST['so_id'];
    $so_date  = $_POST['so_date'];
    $so_total = $_POST['app_all'];
    $so_type  = $_POST['so_type'];
    $so_emp   = $_POST['emp_id'];
    /// insert sales_order_list ///
    $sol_amt   = $_POST['so_amt'];
    $sol_cost  = $_POST['so_cost'];
    $sol_sum   = $_POST['so_sum'];
    $order_quo = $_POST['quo_order'];
    $id_quo    = $_POST['quo_id'];
    $pro_quo   = $_POST['so_pro'];
    /// table sales_order ///
    $arr_sales = array(
        "so_id"    => $so_id,
        "so_date"  => $so_date,
        "so_total" => $so_total,
        "so_type"  => $so_type,
        "so_emp"   => $so_emp
    );
    $insert_sales = $db->insert("sales_order",$arr_sales);
    /// table sales_order_list ///
    foreach($pro_quo as $key => $data){
        $order = $key + 1;
        $arr_sales_list = array(
            "sol_order" => $order,
            "sol_id"    => $so_id,
            "sol_amt"   => $sol_amt[$key],
            "sol_cost"  => $sol_cost[$key],
            "sol_sum"   => $sol_sum[$key],
            "order_quo" => $order_quo[$key],
            "id_quo"    => $id_quo[$key],
            "pro_quo"   => $data
        );
        $insert_sales_list = $db->insert("sales_order_list",$arr_sales_list);
        if ($insert_sales_list) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มใบสั่งขายสำเร็จ');";
            echo "window.location = 'so_list.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มใบสั่งขายไม่สำเร็จ');";
            echo "</script>";
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
                            <h2>ใบสั่งขาย</h2>
                        </div>
                    </div>
                    <form name="quo_form" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>รหัสใบเสนอราคา/ชื่อลูกค้า</label>
                                        <select name="quo_list" id="quo_list" class="form-control custom-select">
                                            <option selected disabled>เลือกรหัสใบเสนอ/ชื่อลูกค้า</option>
                                            <?php foreach ($rs_so as $value) { ?>
                                                <option value="<?= $value['quo_id'] ?>"><?= $value['quo_id'] ?>/<?= $value['cus_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="quo_id" id="quo_id">
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบสั่งขาย</label>
                                        <input type="text" name="so_id" class="form-control" value="<?= $fetch_id['so'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่สั่งขาย</label>
                                        <input type="date" name="so_date" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อพนักงานขาย</label>
                                        <input type="text" name="emp_name" class="form-control" value="<?= $txt_nic ?>" readonly>
                                        <input type="hidden" name="emp_id" value="<?= $txt_id ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>ประเภทชำระเงิน</label>
                                        <select name="so_type" id="so_type" class="form-control custom-select">
                                            <option selected disabled>ชำระเงิน</option>
                                            <option value="เงินสด">เงินสด</option>
                                            <option value="โอนเงิน">โอนเงิน</option>
                                            <option value="บัตรเครดิต">บัตรเครดิต</option>
                                        </select>
                                    </div>
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>รูปภาพ</th>
                                            <th>รายการสินค้า</th>
                                            <th scope="col" width="16%">จำนวน</th>
                                            <th scope="col" width="16%">ราคา/หน่วย</th>
                                            <th scope="col" width="16%">ส่วนลด</th>
                                            <th>เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <tbody class="main">
                                    </tbody>
                                </table>
                                <div class="form-row">
                                    <div class="form-group">
                                        <input type="submit" name="so_insert" class="btn btn-primary" value="บันทึกใบสั่งขาย">
                                        <button type="button" class="btn btn-danger cancel">ยกเลิกรายการใบสั่งขาย</button>
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
            $("#quo_list").on("change", function() {
                var value = $(this).children("option:selected").attr("value");
                $("#quo_id").val(value)
            });
            $('.select').on('click', function(e) {
                e.preventDefault();
                let so_list = $('#quo_id').val();
                let tbody   = $('.main');
                if(tbody.children().length == 0){
                    $.ajax({
                    url: "so_product.php",
                    type: "post",
                    data: {
                        id: so_list
                    },
                    dataType: "json",
                    success: function(response) {
                        for(let x=0;x<response.length;x++){
                            let pro_img      = response[x].pro_img;
                            let quoc_order   = response[x].quoc_order;
                            let quoc_id      = response[x].quoc_id;
                            let pro_id       = response[x].pro_id;
                            let pro_name     = response[x].pro_name;
                            let size_name    = response[x].size_name;
                            let quol_cnt     = response[x].quol_cnt;
                            let quol_cnte    = response[x].quol_cnte;
                            let unit_name    = response[x].unit_name;
                            let quoc_cost    = response[x].quoc_cost;
                            let quoc_coste   = response[x].quoc_coste;
                            let quol_sum     = response[x].quol_sum;
                            let count        = response[x].count;
                            let status_promo = response[x].status_promo;
                            /// ไม่มีจำนวน/ราคาที่แก้ไข ///
                            if(!quol_cnte && !quoc_coste){
                                /// โปรโมชั่น = 0 / null ///
                                if(status_promo==0 || status_promo==null){
                                /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnt+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnt+">"+"</td>"+
                                                  "<td>"+Number(quoc_cost).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_cost+">"+"</td>"+
                                                  "<td>"+''+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number(quol_cnt*quoc_cost).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnt*quoc_cost))+">"+"</td>"+
                                                  "</tr>";
                                    /// ตารางใบสั่งขาย ///
                                    $('.main').append(row_so);
                                }else{
                                    /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnt+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnt+">"+"</td>"+
                                                  "<td>"+Number(quoc_cost).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_cost+">"+"</td>"+
                                                  "<td>"+count+' '+'%'+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number((quol_cnt*quoc_cost)-((quol_cnt*quoc_cost*count)/100)).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnt*quoc_cost)-((quol_cnt*quoc_cost*count)/100))+">"+"</td>"+
                                                  "</tr>";
                                    /// ตารางใบสั่งขาย ///
                                    $('.main').append(row_so);
                                }
                            /// มีจำนวน/ไม่มีราคาที่แก้ไข ///
                            }else if (quol_cnte && !quoc_coste){
                                /// โปรโมชั่น = 0 / null ///
                                if(status_promo==0 || status_promo==null){
                                    /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnte+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnte+">"+"</td>"+
                                                  "<td>"+Number(quoc_cost).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_cost+">"+"</td>"+
                                                  "<td>"+''+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number(quol_cnte*quoc_cost).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnte*quoc_cost))+">"+"</td>"+
                                                  "</tr>";
                                    /// ตารางใบสั่งขาย ///
                                    $('.main').append(row_so);
                                }else{
                                    /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnte+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnte+">"+"</td>"+
                                                  "<td>"+Number(quoc_cost).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_cost+">"+"</td>"+
                                                  "<td>"+count+' '+'%'+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number((quol_cnte*quoc_cost)-((quol_cnte*quoc_cost*count)/100)).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnte*quoc_cost)-((quol_cnte*quoc_cost*count)/100))+">"+"</td>"+
                                                  "</tr>";
                                    /// ตารางใบสั่งขาย ///
                                    $('.main').append(row_so);
                                }
                            /// ไม่มีจำนวน/มีราคาที่แก้ไข ///
                            }else if(!quol_cnte && quoc_coste){
                                /// โปรโมชั่น = 0 / null ///
                                if(status_promo==0 || status_promo==null){
                                    /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnt+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnt+">"+"</td>"+
                                                  "<td>"+Number(quoc_coste).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_coste+">"+"</td>"+
                                                  "<td>"+''+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number(quol_cnt*quoc_coste).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnt*quoc_coste))+">"+"</td>"+
                                                  "</tr>";
                                    /// ตารางใบสั่งขาย ///
                                    $('.main').append(row_so);
                                }else{
                                    /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnt+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnt+">"+"</td>"+
                                                  "<td>"+Number(quoc_coste).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_coste+">"+"</td>"+
                                                  "<td>"+count+' '+'%'+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number((quol_cnt*quoc_coste)-((quol_cnt*quoc_coste*count)/100)).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnt*quoc_coste)-((quol_cnt*quoc_coste*count)/100))+">"+"</td>"+
                                                  "</tr>";
                                    /// ตารางใบสั่งขาย ///
                                    $('.main').append(row_so);
                                }
                            /// มีทั้งคู่ ///
                            }else{
                                /// โปรโมชั่น = 0 / null ///
                                if(status_promo==0 || status_promo==null){
                                /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnte+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnte+">"+"</td>"+
                                                  "<td>"+Number(quoc_coste).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_coste+">"+"</td>"+
                                                  "<td>"+''+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number(quol_cnte*quoc_coste).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnte*quoc_coste))+">"+"</td>"+
                                                  "</tr>";
                                /// ตารางใบสั่งขาย ///
                                $('.main').append(row_so);
                                }else{
                                /// ทำรายการใบสั่งขาย ///
                                    let row_so  = "<tr>"+
                                                  "<td>"+"<img src='image_pro/"+pro_img+"' alt='' width='70px' height='70px'>"+"<input type='hidden' name='quo_order[]' class='form-control' value="+quoc_order+">"+"</td>"+
                                                  "<td>"+pro_name+' '+size_name+"<input type='hidden' name='so_pro[]' class='form-control' value="+pro_id+">"+"</td>"+
                                                  "<td>"+quol_cnte+' '+unit_name+"<input type='hidden' name='so_amt[]' class='form-control' value="+quol_cnte+">"+"</td>"+
                                                  "<td>"+Number(quoc_coste).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_cost[]' class='form-control' value="+quoc_coste+">"+"</td>"+
                                                  "<td>"+count+' '+'%'+"<input type='hidden' name='quo_id[]' class='form-control' value="+quoc_id+">"+"</td>"+
                                                  "<td>"+Number((quol_cnte*quoc_coste)-((quol_cnte*quoc_coste*count)/100)).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='so_sum[]' class='form-control so_sum' value="+((quol_cnte*quoc_coste)-((quol_cnte*quoc_coste*count)/100))+">"+"</td>"+
                                                  "</tr>";
                                /// ตารางใบสั่งขาย ///
                                $('.main').append(row_so);
                                }
                            }
                        }
                        var total = 0;
                        $('input.so_sum').each(function(){
                            var cal = parseFloat($(this).val());
                            total = total + cal;
                        });
                        $('.txt_edit1').text(Number(total).toLocaleString('en')+' '+'บาท');
                        $('.txt_edit2').text(Number(total*0.07).toLocaleString('en')+' '+'บาท');
                        $('.txt_edit3').text(Number(total+(total*0.07)).toLocaleString('en')+' '+'บาท');
                        $('.app_all').val(total+(total*0.07));
                    }
                });
                }else{
                    alert('มีรายการสั่งขายนี้ในตารางอยู่');
                    e.preventDefault();
                }
            });
            $('.cancel').on('click',function(e){
                e.preventDefault();
                $('.main').empty();
                $("#quo_list").prop('selectedIndex',0);
                $("#quo_id").val("");
            });
        });
    </script>
</body>

</html>