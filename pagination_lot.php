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
$txtSQL = "SELECT pro_id,pro_name,size_name,SUM(IFNULL(lot_amount,0)) as tol ,unit_name,IFNULL(quo_pro,0) as count FROM product
INNER JOIN product_size ON (pro_size=size_id)
INNER JOIN convent      ON (pro_id=con_product)
INNER JOIN product_unit ON (con_unts=unit_id)
LEFT  JOIN lot          ON (pro_id=lot_pro)
WHERE status_pro = 1 
GROUP BY lot_pro,pro_id
ORDER BY pro_id ASC 
LIMIT $start,$limit";
$result_lot  =  $db->query($txtSQL);
$output = "";
$output .= '<div class="form-row">
            <div class="form-group col-md-3 mt-auto ml-auto">
            <input type="text" name="inf_lot" id="inf_lot" autocomplete="off" placeholder="ชื่อสินค้า" class="form-control">
            </div>
            <div class="form-group mt-auto mr-2">
            <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
            </div>
            </div>
            <table class="table table-bordered text-center display">
                <thead class="thead-dark">
                    <tr>
                        <th>ชื่อสินค้า/หน่วยสินค้า</th>
                        <th>จำนวน</th>
                        <th>เพิ่มเติม</th>
                    </tr>
                </thead>
            <tbody class="lot">';
foreach ($result_lot as $da_ta) {
    if ($da_ta['tol'] == 0) {
        $output .= '<tr>
        <td>
            ' . $da_ta['pro_name'] . ' ' . $da_ta['size_name'] . '/' . $da_ta['unit_name'] . '
        </td>
        <td class="text-danger">
            ' . $da_ta['tol'] . '
            <input type="hidden" name="shw_cnt" class="form-control shw_cnt" value=' . $da_ta['tol'] . '>
        </td>
        <td>
            <button type="button" class="btn btn-info choose">เลือกข้อมูลสินค้า</button>
        </td>
    </tr>';
    } else {
        $output .= '<tr>
            <td>
                ' . $da_ta['pro_name'] . ' ' . $da_ta['size_name'] . '/' . $da_ta['unit_name'] . '
            </td>
            <td>
                ' . ($da_ta['tol']-$da_ta['count']) . ' 
                <input type="hidden" name="shw_cnt" class="form-control shw_cnt" value=' . $da_ta['tol'] . '>
            </td>
            <td>
                <button type="button" class="btn btn-info choose" value='.$da_ta['pro_id'].'>เลือกข้อมูลสินค้า</button>
                <input type="hidden" name="counter" class="form-control counter" value="0">
            </td>
        </tr>';
    }
}
$output .= '</tbody>
                </table>';
$txtSQL = "SELECT count(pro_id) as total FROM product";
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
<!-- <script src="js/valid.js"></script> -->
<script>
    $(function() {
        var i = 0;
        $('.choose').on('click', function(e) {
            e.preventDefault();
            var par = $(this).closest('tr');
            var id  = $(this).val();
            // var pro = par.find('input.counter').val(++i);
            var cnt = par.find('input.shw_cnt').val();
            if(cnt==false){
                alert('ไม่มีสินค้าในระบบ');
                e.preventDefault();
            }else{
                $.ajax({
                url: "lot_order.php",
                type: "post",
                data: {
                    product: id
                },
                dataType: "json",
                success: function(response) {
                    for(let x=0;x<response.length;x++){
                        let pro_id       = response[x].pro_id;
                        let pro_name     = response[x].pro_name;
                        let size_name    = response[x].size_name;
                        let unit_name    = response[x].unit_name;
                        let pro_price    = response[x].pro_price;
                        let count        = response[x].count;
                        let status_promo = response[x].status_promo;
                        /// โปรโมชั่น = 0 / null ///
                        if(status_promo==0 || status_promo==null){
                            // สร้างแถว รายการใบเสนอราคา ///
                            let row_pro = "<tr>"+
                                      "<td>"+pro_name+size_name+"<input type='hidden' name='pro_id[]' class='form-control pro_tx' value="+pro_id+"></td>"+
                                      "<td>"+"<div class='input-group'><input type='text' name='cnt[]' autocomplete='off' class='form-control text-center num quo_cnt'><div class='input-group-append'><span class='input-group-text' id='basic-addon4'>"+unit_name+"</span></div></div>"+"</td>"+
                                      "<td>"+Number(pro_price).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='quo_cost[]' class='form-control text-center num quo_cost' value="+pro_price+" >"+"</td>"+
                                      "<td>"+''+"<input type='hidden' name='cost' autocomplete='off' class='form-control text-center percent' value='' readonly>"+"</td>"+
                                      "<td>"+"<button type='button' class='btn btn-danger del'><i class='fa fa-minus'></i></button>"+"</td>"+
                                      "</tr>";
                            /// ตารางใบเสนอราคา ///
                            $('.main').append(row_pro);
                        }else{
                            // สร้างแถว รายการใบเสนอราคา ///
                            let row_pro = "<tr>"+
                                      "<td>"+pro_name+size_name+"<input type='hidden' name='pro_id[]' class='form-control pro_tx' value="+pro_id+"></td>"+
                                      "<td>"+"<div class='input-group'><input type='text' name='cnt[]' autocomplete='off' class='form-control text-center num quo_cnt'><div class='input-group-append'><span class='input-group-text' id='basic-addon4'>"+unit_name+"</span></div></div>"+"</td>"+
                                      "<td>"+Number(pro_price).toLocaleString('en')+' '+'บาท'+"<input type='hidden' name='quo_cost[]' class='form-control text-center num quo_cost' value="+pro_price+" >"+"</td>"+
                                      "<td>"+count+' '+'%'+"<input type='hidden' name='cost' autocomplete='off' class='form-control text-center percent' value='' readonly>"+"</td>"+
                                      "<td>"+"<button type='button' class='btn btn-danger del'><i class='fa fa-minus'></i></button>"+"</td>"+
                                      "</tr>";
                            /// ตารางใบเสนอราคา ///
                            $('.main').append(row_pro);
                        }
                        /// ลบ สินค้า ///
                        $('tr').on('click', '.del', function(e) {
                            var re  = $(this).parents("tr").remove();
                            // if(par.find('input.counter').val()==1){
                            //     par.find('input.counter').val(--i);
                            // }else{
                            //     var pro = par.find('input.counter').val(--i);
                            //     var pro = par.find('input.counter').val(--i);
                            // }
                        });
                    }
                }
            });
            }
        });
        $('.select').on('click', function(e) {
            e.preventDefault();
            var txt_pro = $('#inf_lot').val();
            $.ajax({
                url: "lot_pro.php",
                type: "post",
                data: {
                    quo_pro: txt_pro
                },
                success: function(data) {
                    $('.lot').html(data);
                }
            });
        });
    });
</script>