<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['quo_pro'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT pro_id,pro_name,size_name,SUM(IFNULL(lot_amount,0)) as tol ,unit_name FROM product
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent      ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    LEFT  JOIN lot          ON (pro_id=lot_pro)
    WHERE pro_name LIKE '%$txt_search%' AND status_pro = 1
    GROUP BY lot_pro,pro_id
    ORDER BY pro_id ASC ";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <?php if($data['tol'] == 0) {?>
    <tr>
        <td><?=$data['pro_name'] . ' ' . $data['size_name'] . '/' . $data['unit_name']?></td>
        <td class="text-danger">
            <?=$data['tol']?>
            <input type="hidden" name="shw_cnt" class="form-control shw_cnt" value="<?=$data['tol']?>">
        </td>
        <td><button type="button" class="btn btn-info choose">เลือกข้อมูลสินค้า</button></td>
    </tr>
    <?php }else{ ?>
    <tr>
        <td><?=$data['pro_name'] . ' ' . $data['size_name'] . '/' . $data['unit_name']?></td>
        <td>
            <?=$data['tol']?>
            <input type="hidden" name="shw_cnt" class="form-control shw_cnt" value="<?=$data['tol']?>">
        </td>
        <td>
            <button type="button" class="btn btn-info choose" value="<?=$data['pro_id']?>">เลือกข้อมูลสินค้า</button>
            <input type="hidden" name="counter" class="form-control counter" value="0">
        </td>
    </tr>
    <?php } ?>
<?php } ?>
<script>
    $(function() {
        var i = 0;
        $('.choose').on('click', function(e) {
            e.preventDefault();
            let par = $(this).closest('tr');
            var id  = $(this).val();
            var cnt = par.find('input.shw_cnt').val();
            var pro = par.find('input.counter').val(++i);
            if(cnt==false){
                alert('ไม่มีสินค้าในระบบ');
                e.preventDefault();
            }else if(par.find('input.counter').val()!=1){
                alert('มีสินค้านี้ในรายการแล้ว');
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
                                      "<td>"+''+"<input type='hidden' name='cost' autocomplete='off' class='form-control text-center percent' value='' readonly>"+"</td>"+
                                      "<td>"+count+' '+'%'+"<button type='button' class='btn btn-danger del'><i class='fa fa-minus'></i></button>"+"</td>"+
                                      "</tr>";
                            /// ตารางใบเสนอราคา ///
                            $('.main').append(row_pro);
                        }
                        /// ลบ สินค้า ///
                        $('tr').on('click', '.del', function(e) {
                            var re  = $(this).parents("tr").remove();
                            if(par.find('input.counter').val()==1){
                                par.find('input.counter').val(--i);
                            }else{
                                var pro = par.find('input.counter').val(--i);
                                var pro = par.find('input.counter').val(--i);
                            }
                        });
                    }
                }
            });
            }
        });
    });
</script>