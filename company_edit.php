<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if(isset($_SESSION['pos_permit']))
// {
//     if(substr($_SESSION['pos_permit'],4,1) != 1){
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     } 
// }
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['id'];
/// ดึงข้อมูลลูกค้ามาแก้ไข ///
$txtSQL = "SELECT com_name,com_contact,com_email,com_add,com_zip,id_dis,DISTRICT_NAME,ID_AMPHUR,AMPHUR_NAME,ID_PROVINCE,PROVINCE_NAME FROM company  INNER JOIN zipcode     ON (company.id_dis=zipcode.ID_DISTRICT)
                               INNER JOIN district    ON (zipcode.ID_DISTRICT=district.DISTRICT_ID)
                               INNER JOIN amphur      ON (district.ID_AMPHUR=amphur.AMPHUR_ID)
                               INNER JOIN province    ON (amphur.ID_PROVINCE=PROVINCE.PROVINCE_ID) WHERE com_id = '$id'";
$result = $db->query($txtSQL);
$row    = mysqli_fetch_array($result);
/// check เงื่อนไขตอนแก้ไข ///
$row_province = $row['ID_PROVINCE'];
$row_amphur   = $row['ID_AMPHUR'];
/// ดึงข้อมูลจังหวัด ///
$txtSQL = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province ORDER BY PROVINCE_NAME asc";
$query_province = $db->query($txtSQL);
/// ดึงข้อมูลตำบล ///
$txtSQL = "SELECT DISTRICT_ID,DISTRICT_NAME FROM district WHERE ID_AMPHUR = '$row_amphur'";
$query_dis = $db->query($txtSQL);
/// ดึงข้อมูลอำเภอ ///
$txtSQL = "SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE ID_PROVINCE = '$row_province'";
$query_amp = $db->query($txtSQL);
/// แก้ไขข้อมูลบริษัทคู่ค้า ///
if(isset($_POST['com_edit'])){
    $com_name    = $_POST['com_name'];
    $com_contact = $_POST['com_contact'];
    $com_email   = $_POST['com_email'];
    $com_add     = $_POST['com_add'];
    $id_dis      = $_POST['district'];
    $com_zip     = $_POST['zipcode'];
    $com_tel     = $_POST['com_tel'];
    $arr = array(
        "com_name" => $com_name,
        "com_contact" => $com_contact,
        "com_email" => $com_email,
        "com_add" => $com_add,
        "id_dis" => $id_dis,
        "com_zip" => $com_zip
   );
   $where_condition = array(
        "com_id" => $id
   );
   $update = $db->update("company", $arr, $where_condition);
   ///
   if($update){
       $txtSQL = "DELETE FROM company_tel WHERE com_id = '$id'";
       $result = $db->query($txtSQL);
       foreach ($com_tel as  $tel) {
       $arr1 = array(
          "com_id" => $id,
          "com_tel" => $tel
       );
       $update_all = $db->insert("company_tel", $arr1);
        if ($update_all) {
            echo "<script type='text/javascript'>";
            echo "alert('แก้ไขข้อมูลสำเร็จ');";
            echo "window.location = 'company.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
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
                        <!--Card content-->
                        <div class="card-body">
                            <h2>ข้อมูลบริษัทคู่ค้า</h2>
                        </div>
                    </div>
                    <form method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ชื่อบริษัทคู่ค้า</label>
                                        <input type="text" name="com_name" id="com_name" class="form-control" placeholder="ชื่อบริษัทคู่ค้า" value="<?php echo $row['com_name']?>" autocomplete="off">
                                        <span id="validate"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ชื่อผู้ติดต่อ</label>
                                        <input type="text" name="com_contact" class="form-control" placeholder="ชื่อผู้ติดต่อ" value="<?php echo $row['com_contact']?>" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-row">
                                <div class="form-group col-md-7">
                                    <label>อีเมล</label>
                                    <input type="email" name="com_email" id="com_email" class="form-control" placeholder="อีเมล" autocomplete="off" value="<?=$row['com_email']?>">
                                    <span id="validate_email"></span>
                                </div>
                                <div class="form-group col-md-5" id="insert">
                                        <label>เบอร์โทรศัพท์</label>
                                        <?php
                                        $txtSQL = "SELECT com_tel FROM company_tel WHERE com_id = '$id'";
                                        $query_tel = $db->query($txtSQL);
                                        $t = 1;
                                        foreach ($query_tel as $phone) { ?>
                                        <div class="input-group control-group">
                                            <input type="text" name="com_tel[]" placeholder="เบอร์โทรศัพท์" class="form-control name_list" autocomplete="off" value="<?php echo $phone['com_tel'] ?>">
                                            <div class="input-group-btn ml-2">
                                            <?php if ($t == 1) { ?>
                                                <button class="btn btn-success add-more" type="button"><i class="fa fa-plus"></i></button>
                                            <?php } else { ?>
                                                <button class="btn btn-danger remove" type="button"><i class="fa fa-minus"></i></button>
                                            <?php } ?>
                                        </div>
                                        </div>
                                        <?php $t++;
                                        } ?>
                                    </div>
                                </div>
                                <h2>ข้อมูลที่อยู่</h2>
                                <div class="form-group">
                                    <label>ที่อยู่</label>
                                    <textarea class="form-control" name="com_add" rows="4"><?php echo $row['com_add']?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>จังหวัด</label>
                                    <select name="province" id="province" class="form-control custom-select">
                                        <option selected disabled>จังหวัด</option>
                                        <?php foreach ($query_province as $value) { ?>
                                            <option value="<?php echo $value['PROVINCE_ID'] ?>"
                                            <?php
                                                if($row['ID_PROVINCE']==$value['PROVINCE_ID']){
                                                    echo "selected";
                                                }
                                            ?>
                                            ><?php echo $value['PROVINCE_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>อำเภอ</label>
                                    <select name="amphur" id="amphur" class="form-control custom-select">
                                    <option selected disabled>อำเภอ</option>
                                        <?php foreach ($query_amp as $value) { ?>
                                            <option value="<?php echo $value['AMPHUR_ID'] ?>"
                                            <?php
                                                if($row['ID_AMPHUR']==$value['AMPHUR_ID']){
                                                    echo "selected";
                                                }
                                            ?>
                                            ><?php echo $value['AMPHUR_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ตำบล</label>
                                    <select name="district" id="district" class="form-control custom-select">
                                    <option selected disabled>ตำบล</option>
                                        <?php foreach ($query_dis as $value) { ?>
                                            <option value="<?php echo $value['DISTRICT_ID'] ?>"
                                            <?php
                                                if($row['id_dis']==$value['DISTRICT_ID']){
                                                    echo "selected";
                                                }
                                            ?>
                                            ><?php echo $value['DISTRICT_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>รหัสไปรษณีย์</label>
                                    <input type="text" name="zipcode" id="zipcode" class="form-control" value="<?php echo $row['com_zip']?>" readonly>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="com_edit" id="com_edit" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="company.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
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
            // var status = false;
            // $('#com_name').blur(function() {
            //     var com_name = $('#com_name').val();
            //     if (com_name == "") {
            //         $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
            //         $('#validate').addClass("text-danger");
            //         /// remove class ///
            //         $('#validate').removeClass("text-success");
            //         status = false;
            //         return;
            //     }
            //     $.ajax({
            //         type: "post",
            //         url: "check_com.php",
            //         data: {
            //             com_name: com_name
            //         },
            //         success: function(data) {
            //             if (data != '0') {
            //                 $('#validate').html("ไม่สามารถใช้ชื่อนี้ได้");
            //                 $('#validate').addClass("text-danger");
            //                 /// remove class ///
            //                 $('#validate').removeClass("text-success");
            //                 status = false;
            //             } else {
            //                 $('#validate').html("สามารถใช้ชื่อนี้ได้");
            //                 $('#validate').addClass("text-success");
            //                 /// remove class ///
            //                 $('#validate').removeClass("text-danger");
            //                 status = true;
            //             }
            //         }
            //     });
            // });
            // $('#com_insert').click(function(e){
            //     if(status==false){
            //         e.preventDefault();
            //         $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
            //         $('#validate').addClass("text-danger");
            //         $('#validate').removeClass("text-success");
            //     }
            // });
        });
    </script>
</body>

</html>