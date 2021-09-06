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
$txtSQL = "SELECT tran_id,tran_name FROM transport
WHERE status_tran = 1 LIMIT $start,$limit";
$result_tra  =  $db->query($txtSQL);
$output = "";
$output .= '<div class="form-row">
            <div class="form-group col-md-3 mt-auto ml-auto">
            <input type="text" name="inf_tra" id="inf_tra" autocomplete="off" placeholder="ชื่อเส้นทาง" class="form-control">
            </div>
            <div class="form-group mt-auto mr-2">
            <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
            </div>
            </div>
            <table class="table table-bordered text-center display">
                <thead class="thead-dark">
                    <tr>
                        <th>ชื่อเส้นทาง</th>
                        <th>เพิ่มเติม</th>
                    </tr>
                </thead>
            <tbody class="tra">';
foreach ($result_tra as $da_ta) {
        $output .= '<tr>
        <td>' . $da_ta['tran_name'] . '</td>
        <td>
            <button type="button" class="btn btn-info cho" value="' . $da_ta['tran_id'] . '">เลือกชื่อเส้นทาง</button>
        </td>
    </tr>';
}
$output .= '</tbody>
                </table>';
$txtSQL = "SELECT count(tran_id) as total FROM transport WHERE status_tran = 1";
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
            var tran = $(this).val();
            $.ajax({
                url: "de_tra.php",
                type: "post",
                data: {
                    txt: tran
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let tran_id   = response[x].tran_id;
                        let tran_name = response[x].tran_name;
                        /// นำตัวแปรไปใส่ ใน input ที่กำหนด ///
                        $('.tra_id').val(tran_id);
                        $('.tra_name').val(tran_name);
                    }
                }
            });
        });
        $('.select').on('click', function(e) {
            e.preventDefault();
            var txt_tra = $('#inf_tra').val();
            $.ajax({
                url: "search_tra.php",
                type: "post",
                data: {
                    del_tra: txt_tra
                },
                success: function(data) {
                    $('.tra').html(data);
                }
            });
        });
    });
</script>