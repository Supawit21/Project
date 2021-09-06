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
$txtSQL = "SELECT tru_id,tru_reg,tru_type,empty_truck FROM trucks
WHERE status_truck = 1 LIMIT $start,$limit";
$result_tru  =  $db->query($txtSQL);
$output = "";
$output .= '<div class="form-row">
            <div class="form-group col-md-3 mt-auto ml-auto">
            <input type="text" name="inf_reg" id="inf_reg" autocomplete="off" placeholder="เลขทะเบียน" class="form-control">
            </div>
            <div class="form-group mt-auto mr-2">
            <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
            </div>
            </div>
            <table class="table table-bordered text-center display">
                <thead class="thead-dark">
                    <tr>
                        <th>เลขทะเบียนรถ</th>
                        <th>ประเภทรถขนส่ง</th>
                        <th>สถานะรถ</th>
                        <th>เพิ่มเติม</th>
                    </tr>
                </thead>
            <tbody class="tru">';
foreach ($result_tru as $da_ta) {
    if($da_ta['empty_truck'] == 1){
        $output .= '<tr>
        <td>' . $da_ta['tru_reg'] . '</td>
        <td>' . $da_ta['tru_type'] . '</td>
        <td>'.'รถว่าง'.'</td>
        <td>
            <button type="button" class="btn btn-info cho" value="' . $da_ta['tru_id'] . '">เลือกข้อมูลรถขนส่ง</button>
        </td>
    </tr>';
    }else{
        $output .= '<tr>
        <td>' . $da_ta['tru_reg'] . '</td>
        <td>' . $da_ta['tru_type'] . '</td>
        <td class="text-danger">'.'รถไม่ว่าง'.'</td>
        <td>
            <button type="button" class="btn btn-info cho" value="' . $da_ta['tru_id'] . '" disabled>เลือกข้อมูลรถขนส่ง</button>
        </td>
    </tr>';
    }
}
$output .= '</tbody>
                </table>';
$txtSQL = "SELECT count(tru_id) as total FROM trucks where empty_truck = 1 AND status_truck = 1";
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
            var tru = $(this).val();
            $.ajax({
                url: "de_tru.php",
                type: "post",
                data: {
                    txt: tru
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let tru_id   = response[x].tru_id;
                        let tru_reg  = response[x].tru_reg;
                        let tru_type = response[x].tru_type;
                        /// นำตัวแปรไปใส่ ใน input ที่กำหนด ///
                        $('.tru_id').val(tru_id);
                        $('.tru_name').val(tru_reg+"/"+tru_type);
                    }
                }
            });
        });
        $('.select').on('click', function(e) {
            e.preventDefault();
            var txt_tru = $('#inf_reg').val();
            $.ajax({
                url: "search_tru.php",
                type: "post",
                data: {
                    del_tru: txt_tru
                },
                success: function(data) {
                    $('.tru').html(data);
                }
            });
        });
    });
</script>