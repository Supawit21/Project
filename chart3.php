<?php
session_start();
require('config/connect.php');
$db = new DB();
/// ดึงข้อมูลจังหวัด ///
$txtSQL = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province ORDER BY PROVINCE_NAME asc";
$query_province = $db->query($txtSQL);
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
                        <h2>รายงานจำนวนลูกค้าที่มาซื้อสินค้า/ตามเขต/ตามช่วงเวลา</h2>
                    </div>
                </div>
                <div class="card mt-3">
                    <div id="piechart" style="width: 100%; height: 400px;">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3 mt-2 ml-4">
                            <label>วันที่เริ่มต้น</label>
                            <input type="date" name="date_start" id="date_start" class="form-control">
                        </div>
                        <div class="form-group col-md-3 mt-2 ml-1">
                            <label>วันที่สิ้นสุด</label>
                            <input type="date" name="date_end" id="date_end" class="form-control">
                        </div>
                        <div class="form-group col-md-3 mt-2 ml-1">
                            <label>จังหวัด</label>
                            <select name="province" id="province" class="form-control custom-select">
                                <option selected disabled>จังหวัด</option>
                                <?php foreach ($query_province as $value) { ?>
                                    <option value="<?php echo $value['PROVINCE_ID'] ?>"><?php echo $value['PROVINCE_NAME'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- <div class="form-group col-md-2 mt-2 ml-1">
                            <label>จำนวนข้อมูล</label>
                            <input type="text" name="t" id="t" class="form-control">
                        </div> -->
                        <div class="form-group mt-auto">
                            <button type="button" class="btn btn-primary good"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">ชื่อเขต</th>
                                    <th style="text-align:center">จำนวนคนที่ซื้อ</th>
                                    <th style="text-align:center">ราคารวมทั้งหมด</th>
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
            $('.good').on('click', function(e) {
                e.preventDefault();
                var date1        = $('#date_start').val();
                var date2        = $('#date_end').val();
                var province_id  = $('#province').val();
                var total = date1 + date2 + province_id;
                $.ajax({
                    type: "post",
                    url: "report3.php",
                    data: {
                        amp: total
                    },
                    dataType: "json",
                    success: function(response) {
                        for(let x=0;x<response.length;x++){
                            let AMPHUR_NAME  = response[x].AMPHUR_NAME;
                            let CNT_CUSTOMER = response[x].CNT_CUSTOMER;
                            let SUM_TOTAL    = response[x].SUM_TOTAL;
                            /// ตารางรายงานจำนวนลูกค้าที่มาซื้อสินค้าตามเขต ///
                            let row_report = "<tr>" +
                                             "<td class='text-center'>" + (x + 1) + "</td>" +
                                             "<td class='text-center'>" + AMPHUR_NAME  + "</td>" +
                                             "<td class='text-center'>" + CNT_CUSTOMER +' '+'คน'+ "</td>" +
                                             "<td class='text-center'>" + Number(SUM_TOTAL).toLocaleString('en') +' '+'บาท'+ "</td>" +
                                             "</tr>";
                            /// เพิ่มลง class ///
                            $('tbody').append(row_report);
                        }
                        google.charts.load('current', {
                            'packages': ['corechart']
                        });
                        google.charts.setOnLoadCallback(drawChart);

                        function drawChart() {

                            var data = new google.visualization.DataTable();
                            data.addColumn('string', 'Amphur');
                            data.addColumn('number', 'Count');
                            // data.addColumn('number', 'Sum');
                            for (var i = 0; i < response.length; i++) {
                                let AMPHUR_NAME  = response[i].AMPHUR_NAME;
                                let CNT_CUSTOMER = response[i].CNT_CUSTOMER;
                                // let SUM_TOTAL    = response[i].SUM_TOTAL;
                                data.addRow([AMPHUR_NAME,parseInt($.trim(CNT_CUSTOMER))]);
                            }
                            var options = {
                                title: 'รายงานจำนวนลูกค้าที่มาซื้อสินค้า',
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                            chart.draw(data, options);
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>