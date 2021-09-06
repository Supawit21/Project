<?php
session_start();
require('config/connect.php');
$db = new DB();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php') ?>

<body>
    <?php include('includes/sidebar.php') ?>
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-md-10 col-lg-10 col-xl-10 ml-auto">
                <div class="card border-0 mt-5">
                    <div class="card-body d-sm-flex justify-content-between">
                        <h2>รายงานยอดขายประจำปี</h2>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="form-group col-md-4 mt-2 ml-1">
                        <label>เลือกปีที่ต้องการ</label>
                        <select name="year" id="year" class="form-control custom-select">
                            <option value="" selected disabled>เลือกปีที่ต้องการออกรายงาน</option>
                            <?php
                            $this_y = date("Y");
                            $year = date("Y");
                            ?>
                            <?php for ($i = 0; $i <= 10; $i++) { ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                            <?php $year--;
                            } ?>
                        </select>
                    </div>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ชื่อสินค้า/เดือน(12เดือน)</th>
                                    <th style="text-align:center">ม.ค</th>
                                    <th style="text-align:center">ก.พ</th>
                                    <th style="text-align:center">มี.ค</th>
                                    <th style="text-align:center">เม.ษ</th>
                                    <th style="text-align:center">พ.ค</th>
                                    <th style="text-align:center">มิ.ย</th>
                                    <th style="text-align:center">ก.ค</th>
                                    <th style="text-align:center">ส.ค</th>
                                    <th style="text-align:center">ก.ย</th>
                                    <th style="text-align:center">ต.ค</th>
                                    <th style="text-align:center">พ.ย</th>
                                    <th style="text-align:center">ธ.ค</th>
                                    <th style="text-align:center">ผลรวม</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/script.php') ?>
    <script>
        $(function() {
            $('#year').on('change', function(e) {
                e.preventDefault();
                var yy = $(this).val();
                $.ajax({
                    type: "post",
                    url: "report1.php",
                    data: {
                        num_yy: yy
                    },
                    success: function(data) {
                        $('tbody').html(data);
                    }
                });
            });
        });
    </script>
</body>

</html>