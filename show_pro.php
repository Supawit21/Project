<?php
session_start();
require('config/connect.php');
$db = new DB();
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php') ?>

<body>
    <main>
        <nav class="navbar navbar-light bg-light justify-content-between">
            <a class="navbar-brand">บริษัท กงไกร สตีล จำกัด</a>
            <form class="form-inline">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </nav>
        <div class="container">
            <img src="./icon/bqck.jpg" class="rounded mx-auto d-block" alt="test" style="max-width: 100%; padding-top: 70px;">
            <h4 class="display-4 text-center">สินค้าตัวอย่าง</h4>
            <div class="form-row">
                <div class="card ml-4" style="width: 16rem;">
                    <img class="card-img-top" src="https://kongkraisteel.yellowpages.co.th/sites/storage/files/styles/550x550/typmedia/olc/52075265/5ee33456c8803.jpg?itok=60856f8a" alt="Card image cap">
                    <div class="card-body">
                        <strong class="card-text">แป๊ปเหล็กสี่เหลี่ยม</strong>
                    </div>
                    <button type="button" class="btn btn-primary">รายละเอียดสินค้า</button>
                </div>
                <div class="card ml-4" style="width: 16rem;">
                    <img class="card-img-top" src="https://kongkraisteel.yellowpages.co.th/sites/storage/files/styles/550x550/typmedia/olc/52075265/5ee33456daf1d.jpg?itok=60856f8b" alt="Card image cap">
                    <div class="card-body">
                        <strong class="card-text">เหล็กฉาก</strong>
                    </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">รายละเอียดสินค้า</button>
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">รายละเอียดของเหล็กฉาก</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <img src="https://kongkraisteel.yellowpages.co.th/sites/storage/files/users/5/b/2/b/5b2bd812-1ad1-40df-80da-9833e6156aca/%E0%B9%80%E0%B8%AB%E0%B8%A5%E0%B9%87%E0%B8%81%E0%B8%89%E0%B8%B2%E0%B8%81.png" class="rounded mx-auto d-block" alt="test">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card ml-4" style="width: 16rem;">
                    <img class="card-img-top" src="https://kongkraisteel.yellowpages.co.th/sites/storage/files/styles/550x550/typmedia/olc/52075265/5ee33456ed734.jpg?itok=60856f8b" alt="Card image cap">
                    <div class="card-body">
                        <strong class="card-text">เหล็กแบนตัด</strong>
                    </div>
                    <button type="button" class="btn btn-primary">รายละเอียดสินค้า</button>
                </div>
                <div class="card ml-4" style="width: 16rem;">
                    <img class="card-img-top" src="https://kongkraisteel.yellowpages.co.th/sites/storage/files/styles/550x550/typmedia/olc/52075265/5ee33456b3b57.jpg?itok=60856f8a" alt="Card image cap">
                    <div class="card-body">
                        <strong class="card-text">ท่อเหล็กดำ</strong>
                    </div>
                    <button type="button" class="btn btn-primary">รายละเอียดสินค้า</button>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
</body>

</html>