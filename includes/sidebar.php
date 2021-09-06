<div class="wrapper">
  <div class="sidebar">
    <div class="bg_shadow"></div>
    <div class="sidebar_inner">
      <ul class="siderbar_menu">
        <li><a href="index.php" class="nav nav-link">
            <div class="icon"><i class="fas fa-home"></i></div>
            <div class="title">หน้าแรก</div>
          </a>
        </li>
        <li><a href="#" class="emp-btn nav nav-link">
            <div class="icon"><i class="fas fa-user"></i></div>
            <div class="title">พนักงาน</div>
            <div class="arrow"><i class="fas fa-caret-down first"></i></div>
          </a>
          <ul class="accordion">
            <li><a href="department.php"
                      <?php if (isset($_SESSION['pos_permit'])) {
                            if (substr($_SESSION['pos_permit'], 0, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลแผนก</a></li>
            <li><a href="position.php"                      
                      <?php if (isset($_SESSION['pos_permit'])) {
                            if (substr($_SESSION['pos_permit'], 1, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลตำแหน่ง</a></li>
            <li><a href="employee.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 2, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลพนักงาน</a></li>
          </ul>
        </li>
        <li><a href="customer.php"
                      <?php if(isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 3, 1) != 1) {
                              echo "hidden";
                      }}?> class="nav nav-link">
            <div class="icon"><i class="fas fa-user-tie"></i></div>
            <div class="title">ลูกค้า</div>
          </a>
        </li>
        <li><a href="company.php"
                      <?php if(isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'],4,1) != 1) {
                              echo "hidden";
                      }}?> class="nav nav-link">
            <div class="icon"><i class="fas fa-building"></i></div>
            <div class="title">บริษัทคู่ค้า</div>
          </a>
        </li>
        <li><a href="#" class="pro-btn nav nav-link">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="title">สินค้า</div>
            <div class="arrow"><i class="fas fa-caret-down first"></i></div>
          </a>
          <ul class="accordion1">
            <li><a href="protype.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 5, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลประเภทสินค้า</a></li>
            <li><a href="prosize.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 6, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลขนาดสินค้า</a></li>
            <li><a href="prounit.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 7, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลหน่วยสินค้า</a></li>
            <li><a href="product.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 8, 1) != 1) {
                              echo "hidden";
                      }}?>>ข้อมูลสินค้า</a></li>
            <li><a href="cost.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 9, 1) != 1) {
                              echo "hidden";
                      }}?>>ราคาต้นทุนสินค้า</a></li>
          </ul>
        </li>
        <li><a href="convert.php" class="nav nav-link">
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            <div class="title">แปลงหน่วยสินค้า</div>
          </a>
        </li>
        <li><a href="promotion.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 10, 1) != 1) {
                              echo "hidden";
                      }}?> class="nav nav-link">
            <div class="icon"><i class="fas fa-gift"></i></div>
            <div class="title">โปรโมชั่น</div>
          </a>
        </li>
        <li><a href="truck.php" 
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 11, 1) != 1) {
                              echo "hidden";
                      }}?>class="nav nav-link">
            <div class="icon"><i class="fas fa-truck"></i></div>
            <div class="title">รถขนส่ง</div>
          </a>
        </li>
        <li><a href="transport.php"
                      <?php if (isset($_SESSION['pos_permit'])) { 
                            if(substr($_SESSION['pos_permit'], 12, 1) != 1) {
                              echo "hidden";
                      }}?> class="nav nav-link">
            <div class="icon"><i class="fas fa-map"></i></div>
            <div class="title">เส้นทางรถขนส่ง</div>
          </a>
        </li>
        <li><a href="#" class="test-btn nav nav-link">
            <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="title">สั่งซื้อสินค้า</div>
            <div class="arrow"><i class="fas fa-caret-down first"></i></div>
          </a>
          <ul class="accordion2">
            <li><a href="pd_quo.php">เสนอสินค้า</a></li>
            <li><a href="po_list.php">สั่งซื้อสินค้า</a></li>
          </ul>
        </li>
        <li><a href="pr_list.php" class="nav nav-link">
            <div class="icon"><i class="fas fa-receipt"></i></div>
            <div class="title">รับสินค้า</div>
          </a>
        </li>
        <li><a href="#" class="nav nav-link sell-btn">
            <div class="icon"><i class="fas fa-cash-register"></i></div>
            <div class="title">ขายสินค้า</div>
            <div class="arrow"><i class="fas fa-caret-down first"></i></div>
          </a>
          <ul class="accordion7">
            <li><a href="quo_list.php">เสนอราคา</a></li>
            <li><a href="so_list.php">สั่งขายสินค้า</a></li>
          </ul>
        </li>
        <li><a href="delivery.php" class="nav nav-link">
            <div class="icon"><i class="fas fa-shipping-fast"></i></div>
            <div class="title">จัดส่งสินค้า</div>
          </a>
        </li>
        <li><a href="#" class="nav nav-link report">
            <div class="icon"><i class="fas fa-bookmark"></i></div>
            <div class="title">ออกรายงาน</div>
            <div class="arrow"><i class="fas fa-caret-down first"></i></div>
          </a>
          <ul class="accordion8">
            <li><a href="chart1.php">รายงานปริมาณยอดขายประจำปี</a></li>
            <li><a href="chart2.php">รายงานจำนวนสินค้าที่ขายดีตามช่วงเวลาที่กำหนด</a></li>
            <li><a href="chart3.php">รายงานจำนวนลูกค้าที่มาซื้อสินค้า ตามเขต ตามช่วงเวลา</a></li>
            <li><a href="chart4.php">รายงานการได้กำไร-ขาดทุน</a></li>
            <li><a href="chart5.php">รายงานสินค้าที่ค้างส่ง</a></li>
          </ul>
        </li>
      </ul>

    </div>
  </div>
  <div class="main_container">
    <div class="navbar" style="z-index: 9997">
      <div class="hamburger">
        <i class="fas fa-bars"></i>
      </div>
      <div class="logo mr-auto">
        <a href="index.php"><img src="./icon/Logo.jpg" alt="Logo_img" class="rounded-circle" width="35px">&nbsp;Kongkraisteel</a>
      </div>
      <div class="name mr-auto">
        <div class="dropdown show test">
          <a class="nav nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $_SESSION['emp_name']; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item" href="logout.php">ออกจากระบบ</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>