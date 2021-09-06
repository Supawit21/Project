<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 2, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ดึงข้อมูลจังหวัด ///
$txtSQL = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province ORDER BY PROVINCE_NAME asc";
$query_province = $db->query($txtSQL);
/// ดึงข้อมูลตำแหน่ง ///
$txtSQL = "SELECT dep_id,dep_name FROM department";
$result_dep = $db->query($txtSQL);
/// ดึงข้อมูลสัญชาติ ///
$txtSQL = "SELECT nat_id,nat_name FROM nationality";
$result = $db->query($txtSQL);
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
                            <h2>ข้อมูลพนักงาน</h2>
                        </div>
                    </div>
                    <form name="emp_form" id="emp_form" method="post" action="employee_insert.php" enctype="multipart/form-data" novalidate>
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-group text-right">
                                    <input type="file" id="emp_img" name="emp_img" accept="image/*" required>
                                    <img id="show" alt="" width="150" height="150">
                                </div>
                                <div class="form-row">
                                    <div class="from-group col-md-2">
                                        <label>คำนำหน้า</label>
                                        <select name="emp_title" id="emp_title" class="form-control custom-select" required>
                                            <option value="">คำนำหน้า</option>
                                            <option value="นาย">นาย</option>
                                            <option value="นาง">นาง</option>
                                            <option value="นางสาว">นางสาว</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>ชื่อ</label>
                                        <input type="text" name="emp_name" id="emp_name" class="form-control" autocomplete="off" placeholder="ชื่อ" pattern="^[ก-๏\s]+$" required>
                                        <span id="validate"></span>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>นามสกุล</label>
                                        <input type="text" name="emp_surname" class="form-control" autocomplete="off" placeholder="นามสกุล" pattern="^[ก-๏\s]+$" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>ชื่อเล่น</label>
                                        <input type="text" name="emp_nic" class="form-control" autocomplete="off" placeholder="ชื่อเล่น" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>สัญชาติ</label>
                                        <select name="emp_nat" id="emp_nat" class="form-control custom-select" required>
                                            <option value="">กรุณาเลือกสัญชาติ</option>
                                            <?php foreach ($result as $value) { ?>
                                                <option value="<?php echo $value['nat_id'] ?>">
                                                    <?php echo $value['nat_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วัน/เดือน/ปีเกิด</label>
                                        <input type="date" name="emp_bdate" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>รหัสประชาชน</label>
                                        <input type="text" name="emp_iden" id="emp_iden" autocomplete="off" class="form-control" placeholder="รหัสประชาชน" maxlength="13" required>
                                        <span id="error"></span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>อีเมล</label>
                                        <input type="text" name="emp_email" id="emp_email" autocomplete="off" class="form-control" placeholder="อีเมล" required>
                                    </div>
                                    <div class="form-group col-md-4" id="insert">
                                        <label>เบอร์โทรศัพท์</label>
                                        <div class="input-group control-group">
                                            <input type="text" name="emp_tel[]" class="form-control" placeholder="เบอร์โทรศัพท์" autocomplete="off">
                                            <div class="input-group-btn ml-2">
                                                <button class="btn btn-success add-more" type="button"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>เพศ</label>
                                        <div class="mt-3">
                                            <input type="radio" name="emp_gen" id="emp_male" value="ชาย" required>&nbsp;ชาย&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="emp_gen" id="emp_female" value="หญิง" required>&nbsp;หญิง
                                        </div>
                                    </div>
                                </div>
                                <h2>ข้อมูลที่อยู่</h2>
                                <div class="form-group">
                                    <label>ที่อยู่</label>
                                    <textarea class="form-control" name="emp_add" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>จังหวัด</label>
                                    <select name="province" id="province" class="form-control custom-select" required>
                                        <option value="">กรุณาเลือกจังหวัด</option>
                                        <?php foreach ($query_province as $value) { ?>
                                            <option value="<?php echo $value['PROVINCE_ID'] ?>">
                                                <?php echo $value['PROVINCE_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>อำเภอ</label>
                                    <select name="amphur" id="amphur" class="form-control custom-select">
                                        <option>กรุณาเลือกอำเภอ</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ตำบล</label>
                                    <select name="district" id="district" class="form-control custom-select">
                                        <option>กรุณาเลือกตำบล</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>รหัสไปรษณีย์</label>
                                    <input type="text" name="zipcode" id="zipcode" class="form-control" readonly>
                                </div>
                                <h2>ข้อมูลบริษัท</h2>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>แผนก</label>
                                        <select name="department" id="department" class="form-control custom-select" required>
                                            <option value="">กรุณาเลือกแผนก</option>
                                            <?php foreach ($result_dep as $value) { ?>
                                                <option value="<?php echo $value['dep_id'] ?>">
                                                    <?php echo $value['dep_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ตำแหน่ง</label>
                                        <select name="emp_pos" id="emp_pos" class="form-control custom-select">
                                            <option>กรุณาเลือกตำแหน่ง</option>
                                        </select>
                                    </div>
                                </div>
                                <h2>ข้อมูลการเข้าใช้งาน</h2>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ชื่อผู้เข้าใช้งาน</label>
                                        <input type="text" name="emp_user" id="emp_user" autocomplete="off" class="form-control" placeholder="ชื่อผู้เข้าใช้งาน" required>
                                        <span id="validate_user"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>รหัสผ่าน</label>
                                        <input type="password" name="emp_pass" id="emp_pass" class="form-control" placeholder="รหัสผ่าน" required>
                                        <span id="validate_pass"></span>
                                    </div>
                                    <!-- <div class="form-group col-md-4">
                                        <label>ยืนยันรหัสผ่าน</label>
                                        <input type="password" name="emp_con" id="emp_con" class="form-control" placeholder="ยืนยันรหัสผ่าน">
                                        <span id="validate_check"></span>
                                    </div> -->
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="emp_insert" id="emp_insert" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="employee.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
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
                add_tel += '<input type="text" name="emp_tel[]" class="form-control" placeholder="เบอร์โทรศัพท์" autocomplete="off">';
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
            // /// พิมพ์หาข้อมูลสัญชาติ ///
            // var status = false;
            // $('#emp_nation').keyup(function() {
            //     var nation_txt = $('#emp_nation').val();
            //     if (nation_txt != "") {
            //         $.ajax({
            //             url: 'auto_nat.php',
            //             type: 'post',
            //             data: {
            //                 query: nation_txt,
            //             },
            //             success: function(response) {
            //                 $('#show-list').html(response);
            //             },
            //         });
            //     } else {
            //         $('#show-list').html("");
            //         status = false;
            //     }
            // });
            // /// เลือกข้อมูลสัญชาติที่แสดงมาลงในช่อง text ///
            // $(document).on("click", "option", function() {
            //     $("#emp_nation").val($(this).html());
            //     $("#nat_id").val($(this).val())
            //     $("#show-list").html("");
            // });
            /// เลือกคำนำหน้า แล้วให้ช่องเลือกเพศ ตรงกับ คำนำหน้า ///
            $('#emp_title').change(function() {
                var emp_title = $('#emp_title').val()
                if (emp_title == "นาย") {
                    $('#emp_female').attr('disabled', true);
                    $('#emp_male').attr('disabled', false);
                } else if (emp_title == "นาง" || emp_title == "นางสาว") {
                    $('#emp_male').attr('disabled', true);
                    $('#emp_female').attr('disabled', false);
                }
            });
        });
        var emp_file = document.getElementById('emp_img')
        emp_file.onchange = function() {
            var file = emp_file.files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                var result = reader.result;
                document.getElementById('show').src = result;
            }
        }
        //     $('#emp_name').blur(function() {
        //         var emp_name = $('#emp_name').val();
        //         if (emp_name == "") {
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_emp.php",
        //             data: {
        //                 emp_name: emp_name
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
        //     $('#emp_iden').keyup(function() {
        //         var emp_iden = $('#emp_iden').val();
        //         if (emp_iden != "" && emp_iden.length == 13) {
        //             $.ajax({
        //                 type: "post",
        //                 url: "check_emp.php",
        //                 data: {
        //                     iden: emp_iden
        //                 },
        //                 success: function(data) {
        //                     var result = Script_checkID(emp_iden);
        //                     if (result === false) {
        //                         $('#error').html('เลขบัตรประจำตัวประชาชนไม่ถูกต้อง');
        //                         $('#error').addClass('text-danger');
        //                         /// remove class ///
        //                         $('#error').removeClass('text-success');
        //                         status = false;
        //                     } else if (data != '0') {
        //                         $('#error').html('มีเลขบัตรประจำตัวประชาชนนี้ในระบบแล้ว');
        //                         $('#error').addClass('text-danger');
        //                         /// remove class ///
        //                         $('#error').removeClass('text-success');
        //                         status = false;
        //                     } else {
        //                         $('#error').html('เลขบัตรประจำตัวประชาชนถูกต้อง');
        //                         $('#error').addClass('text-success');
        //                         /// remove class ///
        //                         $('#error').removeClass('text-danger');
        //                         status = true;
        //                     }
        //                 }
        //             })
        //         } else {
        //             $('#error').html("");
        //         }
        //     });
        //     $('#emp_email').blur(function() {
        //         var email = $('#emp_email').val();
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
        //             url: "check_emp.php",
        //             data: {
        //                 emp_email: email
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
        //     $('#emp_user').blur(function() {
        //         var username = $('#emp_user').val();
        //         if (username == "") {
        //             $('#validate_user').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate_user').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate_user').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_emp.php",
        //             data: {
        //                 emp_user: username
        //             },
        //             success: function(data) {
        //                 if (data != '0') {
        //                     $('#validate_user').html("มีชื่อผู้ใช้นี้ในระบบแล้ว");
        //                     $('#validate_user').addClass("text-danger");
        //                     /// remove class ///
        //                     $('#validate_user').removeClass("text-success");
        //                     status = false;
        //                 } else {
        //                     $('#validate_user').html("สามารถใช้ชื่อผู้ใช้นี้ได้");
        //                     $('#validate_user').addClass("text-success");
        //                     /// remove class ///
        //                     $('#validate_user').removeClass("text-danger");
        //                     status = true;
        //                 }
        //             }
        //         })
        //     });
        //     $("#emp_pass").blur(function() {
        //         var pass = $("#emp_pass").val();
        //         var test = password(pass);
        //         if (test == false) {
        //             $('#validate_pass').html('รหัสผ่านไม่ถูกต้อง');
        //             $('#validate_pass').addClass('text-danger');
        //             /// remove class ///
        //             $('#validate_pass').removeClass('text-success');
        //             status = false;
        //         } else {
        //             $('#validate_pass').html('รหัสผ่านถูกต้อง');
        //             $('#validate_pass').addClass('text-success');
        //             /// remove class ///
        //             $('#validate_pass').removeClass('text-danger');
        //             status = true;
        //         }
        //     });
        //     $('#emp_con').blur(function() {
        //         var emp_pass = $('#emp_pass').val();
        //         var emp_con = $('#emp_con').val();
        //         if (emp_con == "") {
        //             $('#validate_check').html('');
        //             return;
        //         } else if (emp_pass != emp_con) {
        //             $('#validate_check').html('รหัสผ่านไม่ตรงกัน');
        //             $('#validate_check').addClass('text-danger');
        //             /// remove class ///
        //             $('#validate_check').removeClass('text-success');
        //             status = false;
        //         } else {
        //             $('#validate_check').html('รหัสผ่านตรงกัน');
        //             $('#validate_check').addClass('text-success');
        //             /// remove class ///
        //             $('#validate_check').removeClass('text-danger');
        //             status = true;
        //         }
        //     });
        //     $('#emp_insert').click(function(e){
        //         if(status==false){
        //             e.preventDefault();
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             $('#validate').removeClass("text-success");
        //             ///
        //             $('#show-list').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#show-list').addClass("text-danger");
        //             $('#show-list').removeClass("text-success");
        //             ///
        //             $('#validate_check').html('กรุณากรอกรหัสผ่านยืนยัน');
        //             $('#validate_check').addClass('text-danger');
        //             $('#validate_check').removeClass('text-success');
        //         }
        //     });
        // });
        // function number() {
        //     key = event.keyCode
        //     if (key < 48 || key > 57)
        //         event.returnValue = false;
        // }
        // function IsEmail(emp_email) {
        //     var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        //     if (!regex.test(emp_email)) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }
        // function password(emp_pass) {
        //     var chk_pass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,32}$/;
        //     if (!chk_pass.test(emp_pass)) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }
        // function Script_checkID(emp_iden) {
        //     if (emp_iden.length != 13) {
        //         return false;
        //     }
        //     for (i = 0, sum = 0; i < 12; i++) {
        //         sum += parseFloat(emp_iden.charAt(i)) * (13 - i);
        //     }
        //     if ((11 - sum % 11) % 10 != parseFloat(emp_iden.charAt(12))) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }
        // var emp_file = document.getElementById('emp_img')
        // emp_file.onchange = function() {
        //     var file = emp_file.files[0];
        //     var reader = new FileReader();
        //     reader.readAsDataURL(file);
        //     reader.onload = function() {
        //         var result = reader.result;
        //         document.getElementById('show').src = result;
        //     }
        // }
    </script>
</body>

</html>