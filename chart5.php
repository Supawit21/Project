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
                        <h2>รายงานสินค้าค้างส่ง</h2>
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
                        <div class="form-group mt-auto">
                            <button type="button" class="btn btn-primary good"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">รหัสใบสั่งขาย</th>
                                    <th style="text-align:center">ลำดับรายการ</th>
                                    <th style="text-align:center">รายการสินค้า</th>
                                    <th style="text-align:center">จำนวนที่สั่ง</th>
                                    <th style="text-align:center">จำนวนที่ส่ง</th>
                                    <th style="text-align:center">จำนวนที่คงเหลือ</th>
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
                var date1 = $('#date_start').val()
                var date2 = $('#date_end').val()
                var total = date1 + date2;
                $.ajax({
                    type: "post",
                    url: "report5.php",
                    data: {
                        days: total
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response)
                        for(let x=0;x<response.length;x++){
                            let so_id     = response[x].so_id;
                            let sol_order = response[x].sol_order;
                            let pro_name  = response[x].pro_name;
                            let size_name = response[x].size_name;
                            let sol_amt   = response[x].sol_amt;
                            let unit_name = response[x].unit_name;
                            let sd_cnt    = response[x].sd_cnt;
                            /// ตารางรายงานสินค้าค้างส่ง ///
                            let row_report = "<tr>" +
                                             "<td class='text-center'>" + so_id  + "</td>" +
                                             "<td class='text-center'>" + sol_order  + "</td>" +
                                             "<td class='text-center'>" + pro_name + ' '+ size_name + "</td>" +
                                             "<td class='text-center'>" + sol_amt  + ' '+ unit_name + "</td>" +
                                             "<td class='text-center'>" + sd_cnt  + ' '+ unit_name + "</td>" +
                                             "<td class='text-center'>" + (sol_amt-sd_cnt) + ' ' + unit_name +"</td>" +
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
                            data.addColumn('string', 'Product');
                            data.addColumn('number', 'Count');
                            for (var i = 0; i < response.length; i++) {
                                let pro_name  = response[i].pro_name;
                                let size_name = response[i].size_name;
                                let sol_amt   = response[i].sol_amt;
                                let sd_cnt    = response[i].sd_cnt;
                                data.addRow([pro_name+size_name,parseInt($.trim(sol_amt-sd_cnt))]);
                            }
                            var options = {
                                title: 'รายงานสินค้าที่ค้างส่ง'
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