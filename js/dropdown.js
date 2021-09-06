$(function(){
     $('#province').change(function(){
         var province_id = $(this).val();
         $.ajax({
            type:"post",
            url:"ajax_emp.php",
            data:{id:province_id,function:'province'},
            success: function(data){
                $('#amphur').html(data);
                $('#district').html('');
                $('#zipcode').val('');
            } 
         });
     });
     $('#amphur').change(function(){
        var amphur_id = $(this).val();
        $.ajax({
           type:"post",
           url:"ajax_emp.php",
           data:{id:amphur_id,function:'amphur'},
           success: function(data){
               $('#district').html(data);
               $('#zipcode').val('');
           }
        });
    });
    $('#district').change(function(){
        var district_id = $(this).val();
        $.ajax({
           type:"post",
           url:"ajax_emp.php",
           data:{id:district_id,function:'district'},
           success: function(data){
               $('#zipcode').val(data);
           }
        });
    });
    $('#department').change(function(){
        var department_id = $(this).val();
        $.ajax({
           type:"post",
           url:"ajax_emp.php",
           data:{id:department_id,function:'department'},
           success: function(data){
               $('#emp_pos').html(data);
           }
        });
    });
});