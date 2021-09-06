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
$txtSQL = "SELECT pro_id,pro_name,size_name,IFNULL(lot_amount,0) as amt_lot,pro_amount FROM lot
           RIGHT JOIN product ON (lot_pro=pro_id)
           INNER JOIN product_size ON (pro_size=size_id) WHERE status_pro = 1 AND EXISTS (SELECT cp_pro FROM cost_price WHERE pro_id = cp_pro)
           GROUP BY pro_id ORDER BY lot_amount ASC LIMIT $start,$limit";
$result_pro  =  $db->query($txtSQL);
$output = "";
$output .= '<div class="form-row">
            <div class="form-group col-md-4">
            <label>ชื่อสินค้า</label>
            <input type="text" name="pro_id" id="pro_id" autocomplete="off" placeholder="สินค้า" class="form-control">
            </div>
            <div class="form-group mt-auto mr-2">
            <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
            </div>
            <div class="form-group col-md-3 mt-auto ml-auto">
            <label>จำนวนชื่อบริษัทที่จะเสนอ</label>
            <input type="text" name="cnt_com" id="cnt_com" autocomplete="off" placeholder="จำนวน" class="form-control num">
            </div>
            </div>
            <table class="table table-bordered text-center display">
                <thead class="thead-dark">
                    <tr>
                        <th>สินค้า</th>
                        <th>จำนวน</th>
                        <th>เพิ่มเติม</th>
                    </tr>
                </thead>
            <tbody class="pro">';
foreach ($result_pro as $da_ta) {
    if($da_ta['pro_amount']>$da_ta['amt_lot']){
        $output .= '<tr>
        <td>' . $da_ta['pro_name'] . ' ' . $da_ta['size_name'] . '</td>
        <td class="text-danger">' . $da_ta['amt_lot'] . '</td>
        <td>
            <button type="button" class="btn btn-danger choose" value="' . $da_ta['pro_id'] . '">แสดงรายชื่อบริษัท</button>
            <input type="hidden" name="counter" class="form-control counter" value="0">
        </td>
    </tr>';
    }else{
        $output .= '<tr>
        <td>' . $da_ta['pro_name'] . ' ' . $da_ta['size_name'] . '</td>
        <td>' . $da_ta['amt_lot'] . '</td>
        <td>
            <button type="button" class="btn btn-danger choose" value="' . $da_ta['pro_id'] . '">แสดงรายชื่อบริษัท</button>
            <input type="hidden" name="counter" class="form-control counter" value="0">
        </td>
    </tr>';
    }
}
$output .= '</tbody>
                </table>';
$txtSQL = "SELECT count(pro_id) as total FROM product where status_pro = 1";
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
<script src="js/valid.js"></script>
<script>
    $(function() {
        $('.choose').on('click', function(e) {
            e.preventDefault();
            var par = $(this).closest('tr');
            var product1 = $(this).val();
            var txt_cnt = $('#cnt_com').val();
            var toa = product1 + txt_cnt;
            $.ajax({
            url: "search_order.php",
            type: "post",
            data: {
                order: toa
            },
                success: function(data) {
                    $('.main').append(data);
                }
            });
            
        });
        $('.select').on('click', function(e) {
            e.preventDefault();
            var txt_pro = $('#pro_id').val();
            $.ajax({
                url: "pro_quo.php",
                type: "post",
                data: {
                    quo_pro: txt_pro
                },
                success: function(data) {
                    $('.pro').html(data);
                }
            });
        });
    });
</script>