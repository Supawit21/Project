<?php

require_once __DIR__ . '/vendor/autoload.php';
require('config/connect.php');
$db = new DB();
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['po_id'];
/// sql หัวใบสั่งซื้อ ///
$txtSQL = "SELECT po_id,po_start,po_end,pol_com,emp_name,com_id,com_name,com_add,ZIP_CODE,DISTRICT_NAME,AMPHUR_NAME,PROVINCE_NAME,po_total FROM po_purchase
INNER JOIN employee ON (po_emp=emp_id)
INNER JOIN pol_purchase ON (po_id=pol_id)
INNER JOIN proposed_cpl ON (pol_com=cpl_com)
INNER JOIN cost_price ON (cpl_com=cp_com)
INNER JOIN company  ON (cp_com=com_id)
INNER JOIN zipcode     ON (company.id_dis=zipcode.ID_DISTRICT)
INNER JOIN district    ON (zipcode.ID_DISTRICT=district.DISTRICT_ID)
INNER JOIN amphur      ON (district.ID_AMPHUR=amphur.AMPHUR_ID)
INNER JOIN province    ON (amphur.ID_PROVINCE=PROVINCE.PROVINCE_ID)
WHERE po_id = '$id'
GROUP BY po_id";
$rs_po = $db->query($txtSQL);
$fetch_head = mysqli_fetch_array($rs_po);
/// sql รายการสินค้า ///
$txtSQL = "SELECT pro_id,pro_name,size_name,pol_amount,unit_name,p1.cpl_price,pol_all FROM pol_purchase
INNER JOIN proposed_cpl  as p1  ON (pol_por=p1.cpl_order)
INNER JOIN proposed_cpl  ON (pol_pdq=p1.cpl_id)
INNER JOIN pdl_quotation as pd1 ON (p1.cpl_order=pd1.pdl_order)
INNER JOIN pdl_quotation ON (p1.cpl_id=pd1.pdl_id)
INNER JOIN cost_price    ON (p1.cpl_pro=cp_pro)
INNER JOIN product       ON (cp_pro=pro_id)
INNER JOIN product_size  ON (pro_size=size_id)
INNER JOIN product_unit  ON (pd1.pdl_unit=unit_id)
WHERE pol_id = '$id' AND p1.cpl_status = 1
GROUP BY pro_id";
$rs_pro = $db->query($txtSQL);
$content1 = "";
$count = 1;
foreach($rs_pro as $value){
        $content1 .= '<tr>
                      <td>'.$count.'</td>
                      <td>'.$value['pro_name'].' '.$value['size_name'].'</td>
                      <td>'.$value['pol_amount'].' '.$value['unit_name'].'</td>
                      <td>'.number_format($value['cpl_price']).' '.'บาท'.'</td>
                      <td>'.number_format($value['pol_all']).' '.'บาท'.'</td>
                      </tr>';
                      $count++;
                      $x = $x + ($value['pol_all']);
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
    <span><strong>บริษัท กงไกร สตีล จำกัด</strong></span><h3 class="name">ใบสั่งซื้อสินค้า</h3><br>
    &nbsp;<span>KONGKRAI STEEL CO., LTD.</span><hr>
    <span><strong>ชื่อบริษัท:</strong></span>&nbsp;<span>'.$fetch_head['com_name'].'</span><h4>เลขที่ใบสั่งซื้อ:&nbsp;<span style="font-weight:normal;">'.$fetch_head['po_id'].'</span></h4><br>
    <span><strong>ที่อยู่:</strong></span>&nbsp;<span>'.$fetch_head['com_add'].' ต.'.$fetch_head['DISTRICT_NAME'].' อ.'.$fetch_head['AMPHUR_NAME'].'</span><h4 style="position:absolute;left:545px;top:165px;">วันที่สั่งซื้อ:&nbsp;<span style="font-weight:normal;">'.$fetch_head['po_start'].'</span></h4><br>
    <span>จ.'.$fetch_head['PROVINCE_NAME'].' '.$fetch_head['ZIP_CODE'].'</span><h4 style="position:absolute;left:545px;top:193px;">วันที่ครบกำหนด:&nbsp;<span style="font-weight:normal;">'.$fetch_head['po_end'].'</span></h4><br>';
    $head .= '<span>โทรศัพท์&nbsp;</span>';
    $com_id = $fetch_head['com_id'];
    $txtSQL = "SELECT com_tel FROM company_tel INNER JOIN company ON (company_tel.com_id=company.com_id) WHERE company.com_id = '$com_id'";
    $rs_tel = $db->query($txtSQL);
    foreach($rs_tel as $tel){
        $head .= $tel['com_tel'].' ';
    }
    $head .= '<h4 style="position:absolute;left:545px;top:220px;">พนักงานสั่งซื้อ:&nbsp;<span style="font-weight:normal;">คุณ'.$fetch_head['emp_name'].'</span></h4>'; 
    $content = '<table width="100%" style="font-size:14pt;text-align:center;margin-top:50px;">
    <thead>
    <tr>
        <th>ลำดับ</th>
        <th>&nbsp;รายละเอียดสินค้า</th>
        <th>จำนวน&nbsp;หน่วยนับ</th>
        <th>ราคาต่อหน่วย</th>
        <th>รวมเงิน</th>
    </tr>
    </thead>
    <tbody>'; 
    $all_sum = $x + ($x*0.07);
    $end = '</tbody>
    </table>
    <hr style="margin-top:450px">
    <div style="position:absolute;right:80px;top:870px">
    <div><strong>รวมราคาสินค้า:</strong>&nbsp;'.number_format($x).' '.'บาท'.'</div>
    <div><strong>ภาษีมูลเพิ่ม (7%):</strong>&nbsp;'.number_format($x*0.07).' '.'บาท'.'</div>
    <div><strong>รวมเงินทั้งหมด:</strong>&nbsp;'.number_format($fetch_head['po_total']).' '.'บาท'.'</div>
    </div>';



$mpdf->WriteHTML($head);
$mpdf->WriteHTML($content);
$mpdf->WriteHTML($content1);
$mpdf->WriteHTML($end);
$mpdf->Output();
?>    