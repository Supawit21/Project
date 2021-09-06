<?php
require('config/connect.php');
$db = new DB();
if (isset($_POST['page_no'])) {
    $page_no = $_POST['page_no'];
} else {
    $page_no = 1;
}
$limit = 5;
$start    = ($page_no - 1) * $limit;
$previous =  $page_no - 1;
$next     =  $page_no + 1;
/// ดึงข้อมูลสินค้าที่ต่ำกว่าจุดสั่งซื้อและสินค้าทั้งหมด ///
$txtSQL = "SELECT cus_id,cus_name FROM customer WHERE status_cus = 1 LIMIT $start,$limit";
$result_cus  =  $db->query($txtSQL);
$output = "";
$output .= '<div class="form-row">
            <div class="form-group col-md-3 mt-auto ml-auto">
            <input type="text" name="inf_cus" id="inf_cus" autocomplete="off" placeholder="รหัสลูกค้า" class="form-control">
            </div>
            <div class="form-group mt-auto mr-2">
            <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
            </div>
            </div>
            <table class="table table-bordered text-center display">
                <thead class="thead-dark">
                    <tr>
                        <th>รหัสลูกค้า</th>
                        <th>ชื่อลูกค้า</th>
                        <th>เพิ่มเติม</th>
                    </tr>
                </thead>
            <tbody class="cus">';
foreach ($result_cus as $da_ta) {
    $output .= '<tr>
                <td>' . $da_ta['cus_id'] . '</td>
                <td>' . $da_ta['cus_name'] . '</td>
                <td>
                    <button type="button" class="btn btn-info cho" value="' . $da_ta['cus_id'] . '">เลือกข้อมูลลูกค้า</button>
                </td>
            </tr>';
}
$output .= '</tbody>
                </table>';
$txtSQL = "SELECT count(cus_id) as total FROM customer where status_cus = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_assoc($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
$output .= '<nav aria-label="Page navigation example"><ul class="pagination">';
if ($page_no > 1) {
    $output .= '<li class="page-item"><a class="page-link" id="' . $previous . '">Previous</a></li>';
} else {
    $output .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Previous</a></li>';
}
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page_no) {
        $active = "active";
    } else {
        $active = "";
    }
    $output .= '<li class="page-item ' . $active . '"><a class="page-link" id="' . $i . '" href="">' . $i . '</a></li>';
}
if ($page_no < $total_pages) {
    $output .= '<li class="page-item"><a class="page-link" id="' . $next . '">Next</a></li>';
} else {
    $output .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Next</a></li>';
}

$output .= '</ul></nav>';

echo $output;
?>
<script>
    $(function() {
        $('.cho').on('click', function(e) {
            e.preventDefault();
            var cus = $(this).val();
            $.ajax({
                url: "quo_cus.php",
                type: "post",
                data: {
                    txt: cus
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let cus_id   = response[x].cus_id;
                        let cus_name = response[x].cus_name;
                        /// นำตัวแปรไปใส่ ใน input ที่กำหนด ///
                        $('.txt_id').val(cus_id);
                        $('.txt_name').val(cus_name);
                    }
                }
            });
        });
        $('.select').on('click', function(e) {
            e.preventDefault();
            var txt_cus = $('#inf_cus').val();
            $.ajax({
                url: "pag_cus.php",
                type: "post",
                data: {
                    quo_cus: txt_cus
                },
                success: function(data) {
                    $('.cus').html(data);
                }
            });
        });
    });
</script>