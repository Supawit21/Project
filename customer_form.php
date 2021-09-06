<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 3, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ดึงข้อมูลจังหวัด ///
$txtSQL = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province ORDER BY PROVINCE_NAME asc";
$query_province = $db->query($txtSQL);
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
                        <!--Card content-->
                        <div class="card-body">
                            <h2>ข้อมูลลูกค้า</h2>
                        </div>
                    </div>
                    <form method="post" action="customer_insert.php">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="from-group col-md-2">
                                        <label>คำนำหน้า</label>
                                        <select name="cus_title" class="form-control custom-select">
                                            <option selected disabled>คำนำหน้า</option>
                                            <option value="นาย">นาย</option>
                                            <option value="นาง">นาง</option>
                                            <option value="นางสาว">นางสาว</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>ชื่อ</label>
                                        <input type="text" name="cus_name" id="cus_name" class="form-control" placeholder="ชื่อ" autocomplete="off">
                                        <span id="validate"></span>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>นามสกุล</label>
                                        <input type="text" name="cus_surname" class="form-control" placeholder="นามสกุล" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-row">
                                <div class="form-group col-md-7">
                                    <label>อีเมล</label>
                                    <input type="email" name="cus_email" id="cus_email" class="form-control" placeholder="อีเมล" autocomplete="off">
                                    <span id="validate_email"></span>
                                </div>
                                    <div class="form-group col-md-5" id="insert">
                                        <label>เบอร์โทรศัพท์</label>
                                        <div class="input-group control-group">
                                            <input type="text" name="cus_tel[]" placeholder="เบอร์โทรศัพท์" class="form-control name_list" autocomplete="off">
                                            <div class="input-group-btn ml-2">
                                            <button class="btn btn-success add-more" type="button"><i class="fa fa-plus"></i></button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <h2>ข้อมูลที่อยู่</h2>
                                <div class="form-group">
                                    <label>ที่อยู่</label>
                                    <textarea class="form-control" name="cus_add" rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>จังหวัด</label>
                                    <select name="province" id="province" class="form-control custom-select">
                                        <option selected disabled>จังหวัด</option>
                                        <?php foreach ($query_province as $value) { ?>
                                            <option value="<?php echo $value['PROVINCE_ID'] ?>"><?php echo $value['PROVINCE_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>อำเภอ</label>
                                    <select name="amphur" id="amphur" class="form-control custom-select">
                                        <option>อำเภอ</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ตำบล</label>
                                    <select name="district" id="district" class="form-control custom-select">
                                        <option>ตำบล</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>รหัสไปรษณีย์</label>
                                    <input type="text" name="zipcode" id="zipcode" class="form-control" readonly>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="cus_insert" id="cus_insert" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="customer.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
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
    <script src="js/dropdown.js"></script>
    <script>
        $(document).ready(function() {
            var p = 1;
            $(".add-more").click(function(e) {
                e.preventDefault();
                p++;
                var add_tel = '<div class="control-group input-group delete">';
                add_tel += '<input type="text" name="cus_tel[]" placeholder="เบอร์โทรศัพท์" class="form-control name_list" autocomplete="off">';
                add_tel += '<div class="input-group-btn ml-2">';
                add_tel += '<button class="btn btn-danger remove" type="button"><i class="fa fa-minus"></i></button>';
                add_tel += '</div>';
                add_tel += '</div>';
                $('#insert').append(add_tel);
            });
            $('#insert').on("click", ".remove", function() {
                $(this).parents(".delete").remove();
                p--;
            });
        });
        //     var status = false;
        //     $('#cus_name').blur(function() {
        //         var cus_name = $('#cus_name').val();
        //         if (cus_name == "") {
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_cus.php",
        //             data: {
        //                 cus_name: cus_name
        //             },
        //             success: function(data) {
        //                 if (data != '0') {
        //                     $('#validate').html("ไม่สามารถใช้ชื่อนี้ได้");
        //                     $('#validate').addClass("text-danger");
        //                     /// remove class ///
        //                     $('#validate').removeClass("text-success");
        //                     status = false;
        //                 } else {
        //                     $('#validate').html("สามารถใช้ชื่อนี้ได้");
        //                     $('#validate').addClass("text-success");
        //                     /// remove class ///
        //                     $('#validate').removeClass("text-danger");
        //                     status = true;
        //                 }
        //             }
        //         });
        //     });
        //     $('#cus_email').blur(function() {
        //         var email = $('#cus_email').val();
        //         if (email == "") {
        //             $('#validate_email').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate_email').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate_email').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_cus.php",
        //             data: {
        //                 cus_email: email
        //             },
        //             success: function(data) {
        //                 var result = IsEmail(email);
        //                 if (result == false) {
        //                     $('#validate_email').html('อีเมลไม่ถูกต้อง');
        //                     $('#validate_email').addClass('text-danger');
        //                     /// remove class ///
        //                     $('#validate_email').removeClass('text-success');
        //                     status = false;
        //                 } else if (data != '0') {
        //                     $('#validate_email').html('มีอีเมลนี้ในระบบแล้ว');
        //                     $('#validate_email').addClass('text-danger');
        //                     /// remove class ///
        //                     $('#validate_email').removeClass('text-success');
        //                     status = false;
        //                 } else {
        //                     $('#validate_email').html('อีเมลถูกต้อง');
        //                     $('#validate_email').addClass('text-success');
        //                     /// remove class ///
        //                     $('#validate_email').removeClass('text-danger');
        //                     status = true;
        //                 }
        //             }
        //         })
        //     });
        //     $('#cus_insert').click(function(e) {
        //         if (status == false) {
        //             e.preventDefault();
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             $('#validate').removeClass("text-success");
        //         }
        //     });
        // });

        // function IsEmail(emp_email) {
        //     var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        //     if (!regex.test(emp_email)) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }

        // function number() {
        //     key = event.keyCode
        //     if (key < 48 || key > 57)
        //         event.returnValue = false;
        // }
    </script>
</body>

</html>