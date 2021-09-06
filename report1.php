<?php
require('config/connect.php');
$db = new DB();
$year =  $_POST['num_yy'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($year)) {
    $txtSQL = "SELECT pro_id,pro_name,size_name,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-01' ) ),0) AS Jan,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-02' ) ),0) AS Feb,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-03' ) ),0) AS Mar,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-04' ) ),0) AS Apr,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-05' ) ),0) AS May,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-06' ) ),0) AS Jun,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-07' ) ),0) AS Jul,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-08' ) ),0) AS Aug,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-09' ) ),0) AS Sep,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-10' ) ),0) AS Oct,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-11' ) ),0) AS Nov,
    IFNULL(SUM( sal.price * ( sal.yymm = '$year-12' ) ),0) AS 'Dec',
		IFNULL(SUM( sal.price * ( sal.YY   = '$year')),0) AS Yea
    FROM product 
    INNER JOIN product_size ON (pro_size=size_id)
    LEFT JOIN ( SELECT quoc_pro,SUM( sol_amt * sol_cost ) AS price,SUBSTR( so_date, 1, 7 ) AS yymm,SUBSTR(so_date,1,4) AS YY
      FROM
      sales_order
      INNER JOIN sales_order_list ON so_id = sol_id
      INNER JOIN quotation_cost ON ( quoc_order=order_quo AND quoc_id=id_quo AND quoc_pro=pro_quo ) 
WHERE
    YEAR ( so_date ) = '$year'
    GROUP BY quoc_pro,sol_order,sol_id) AS sal ON quoc_pro = pro_id
    GROUP BY pro_id";
    $result_sal = $db->query($txtSQL);
}
?>
<?php
foreach ($result_sal as $data) {
?>
    <tr>
        <td><?=$data['pro_name'].' '.$data['size_name']?></td>
        <td><?=number_format($data['Jan'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Feb'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Mar'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Apr'],2).' '.'บาท'?></td>
        <td><?=number_format($data['May'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Jun'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Jul'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Aug'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Sep'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Oct'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Nov'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Dec'],2).' '.'บาท'?></td>
        <td><?=number_format($data['Yea'],2).' '.'บาท'?></td>
        <?php $a = $a + $data['Jan']?>
        <?php $b = $b + $data['Feb']?>
        <?php $c = $c + $data['Mar']?>
        <?php $d = $d + $data['Apr']?>
        <?php $e = $e + $data['May']?>
        <?php $f = $f + $data['Jun']?>
        <?php $g = $g + $data['Jul']?>
        <?php $h = $h + $data['Aug']?>
        <?php $i = $i + $data['Sep']?>
        <?php $j = $j + $data['Oct']?>
        <?php $k = $k + $data['Nov']?>
        <?php $l = $l + $data['Dec']?>
        <?php $m = $m + $data['Yea']?>
    </tr>
<?php
} ?>
<tr>
<td>ผลรวม</td>
<td><?=number_format($a,2).' '.'บาท'?></td>
<td><?=number_format($b,2).' '.'บาท'?></td>
<td><?=number_format($c,2).' '.'บาท'?></td>
<td><?=number_format($d,2).' '.'บาท'?></td>
<td><?=number_format($e,2).' '.'บาท'?></td>
<td><?=number_format($f,2).' '.'บาท'?></td>
<td><?=number_format($g,2).' '.'บาท'?></td>
<td><?=number_format($h,2).' '.'บาท'?></td>
<td><?=number_format($i,2).' '.'บาท'?></td>
<td><?=number_format($j,2).' '.'บาท'?></td>
<td><?=number_format($k,2).' '.'บาท'?></td>
<td><?=number_format($l,2).' '.'บาท'?></td>
<td><?=number_format($m,2).' '.'บาท'?></td>
</tr>