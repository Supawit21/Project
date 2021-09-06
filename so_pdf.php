<?php

require_once __DIR__ . '/vendor/autoload.php';
require('config/connect.php');
$db = new DB();
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['so_id'];
/// sql หัวใบเสนอราคา ///
$txtSQL = "SELECT so_id,so_date,cus_id,cus_title,cus_name,cus_add,emp_name,quo_tol,ZIP_CODE,DISTRICT_NAME,AMPHUR_NAME,PROVINCE_NAME FROM sales_order
INNER JOIN sales_order_list ON (so_id=sol_id)
INNER JOIN quotation_cost as qc1 ON (order_quo=qc1.quoc_order)
INNER JOIN quotation_cost ON (id_quo=qc1.quoc_id)
INNER JOIN quotation_list as ql1 ON (qc1.quoc_order=ql1.quol_order)
INNER JOIN quotation_list ON (qc1.quoc_id=ql1.quol_id)
INNER JOIN quotation ON (ql1.quol_id=quo_id)
INNER JOIN customer ON (quo_cus=cus_id)
INNER JOIN employee ON (quo_emp=emp_id)
INNER JOIN zipcode  ON (customer.id_dis=zipcode.ID_DISTRICT)
INNER JOIN district ON (zipcode.ID_DISTRICT=district.DISTRICT_ID)
INNER JOIN amphur   ON (district.ID_AMPHUR=amphur.AMPHUR_ID)
INNER JOIN province ON (amphur.ID_PROVINCE=province.PROVINCE_ID)
WHERE so_id = '$id'
GROUP BY so_id";
$rs_so = $db->query($txtSQL);
$fetch_head = mysqli_fetch_assoc($rs_so);
/// sql รายการสินค้า ///
$txtSQL = "SELECT pro_name,size_name,unit_name,count,status_promo,s1.sol_amt,s1.sol_cost,s1.sol_sum FROM product
INNER JOIN convent ON (pro_id=con_product)
INNER JOIN product_unit ON (con_unts=unit_id)
INNER JOIN product_size ON (pro_size=size_id)
LEFT JOIN list_promotion ON (pro_id=list_promo)
LEFT JOIN promotion ON (list_id=promo_id)
INNER JOIN quotation_cost ON (pro_id=quoc_pro)
INNER JOIN sales_order_list as s1 ON (quoc_id=s1.id_quo)
INNER JOIN sales_order_list ON (quoc_order=s1.order_quo)
WHERE s1.sol_id = '$id' 
GROUP BY pro_id";
$rs_pro = $db->query($txtSQL);
$content1 = "";
$count = 1;
foreach($rs_pro as $value){
    if($value['status_promo']==NULL||$value['status_promo']==0){
        $content1 .= '<tr>
        <td>'.$count.'</td>
        <td>'.$value['pro_name'].' '.$value['size_name'].'</td>
        <td>'.$value['sol_amt'].' '.$value['unit_name'].'</td>
        <td>'.number_format($value['sol_cost']).' '.'บาท'.'</td>
        <td>'.''.'</td>
        <td>'.number_format($value['sol_sum']).' '.'บาท'.'</td>
        </tr>';
    }else{
        $content1 .= '<tr>
        <td>'.$count.'</td>
        <td>'.$value['pro_name'].' '.$value['size_name'].'</td>
        <td>'.$value['sol_amt'].' '.$value['unit_name'].'</td>
        <td>'.number_format($value['sol_cost']).' '.'บาท'.'</td>
        <td>'.$value['count'].' '.'%'.'</td>
        <td>'.number_format($value['sol_sum']).' '.'บาท'.'</td>
        </tr>';
    }
    $count++;
    $x = $x + ($value['sol_sum']);
}
/// แบบฟอร์ม pdf ///
$mpdf = new \Mpdf\Mpdf();
$head = '
<style>
body{
    font-family: "THSarabun";
    font-size: 16pt;
}
img{
    float:left;
}
.name{
    position: absolute;
    left: 645px;
}
h4{
    position: absolute;
    left: 545px;
    top: 135px;
}
table { border-collapse:collapse; }
table thead th { border-bottom: 1px solid #000; }
tbody { margin-top: 50px;}
</style>          
    <img src="./icon/Logo.jpg" width="50px">     
    <span><strong>บริษัท กงไกร สตีล จำกัด</strong></span><h3 class="name">ใบเสร็จ</h3><br>
    &nbsp;<span>KONGKRAI STEEL CO., LTD.</span><hr>
    <span><strong>ชื่อลูกค้า:</strong></span>&nbsp;<span>'.$fetch_head['cus_title'].' '.$fetch_head['cus_name'].'</span><h4>เลขที่ใบสั่งขาย:&nbsp;<span style="font-weight:normal;">'.$fetch_head['so_id'].'</span></h4><br>
    <span><strong>ที่อยู่:</strong></span>&nbsp;<span>'.$fetch_head['cus_add'].' ต.'.$fetch_head['DISTRICT_NAME'].' อ.'.$fetch_head['AMPHUR_NAME'].'</span><h4 style="position:absolute;left:545px;top:165px;">วันที่สั่งขาย:&nbsp;<span style="font-weight:normal;">'.$fetch_head['so_date'].'</span></h4><br>
    <span>จ.'.$fetch_head['PROVINCE_NAME'].' '.$fetch_head['ZIP_CODE'].'</span><h4 style="position:absolute;left:545px;top:193px;">พนักงานที่สั่งขาย:&nbsp;<span style="font-weight:normal;">'.$fetch_head['emp_name'].'</span></h4><br>';
    $head .= '<span>โทรศัพท์&nbsp;</span>';
    $cus_id = $fetch_head['cus_id'];
    $txtSQL = "SELECT cus_tel FROM customer_tel INNER JOIN customer ON (customer_tel.cus_id=customer.cus_id) WHERE customer.cus_id = '$cus_id'";
    $rs_tel = $db->query($txtSQL);
    foreach($rs_tel as $tel){
        $head .= $tel['cus_tel'].' ';
    }
    $content = '<table width="100%" style="font-size:14pt;text-align:center;margin-top:50px;">
    <thead>
    <tr>
        <th>ลำดับ</th>
        <th>&nbsp;รายละเอียดสินค้า</th>
        <th>จำนวน&nbsp;หน่วยนับ</th>
        <th>ราคาต่อหน่วย</th>
        <th>ส่วนลด</th>
        <th>รวมเงิน</th>
    </tr>
    </thead>
    <tbody>'; 
    $end = '</tbody>
    </table>
    <hr style="margin-top:450px">
    <div style="position:absolute;right:80px;top:870px">
    <div><strong>รวมราคาสินค้า:</strong>&nbsp;'.number_format($x).' '.'บาท'.'</div>
    <div><strong>ภาษีมูลเพิ่ม (7%):</strong>&nbsp;'.number_format($x*0.07).' '.'บาท'.'</div>
    <div><strong>รวมเงินทั้งหมด:</strong>&nbsp;'.number_format($x+($x*0.07)).' '.'บาท'.'</div>
    </div>';



$mpdf->WriteHTML($head);
$mpdf->WriteHTML($content);
$mpdf->WriteHTML($content1);
$mpdf->WriteHTML($end);
$mpdf->Output();
?>    