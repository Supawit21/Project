$(function(){
    $(".emp-btn").click(function(){
      $(".siderbar_menu li").toggleClass("show");
      //$(this).addClass("active");
    });

    $(".pro-btn").click(function(){
      $(".siderbar_menu li").toggleClass("active1");
      //$(this).addClass("active");
    });

    $(".test-btn").click(function(){
      $(".siderbar_menu li").toggleClass("tester");
    });

    $(".sell-btn").on('click',function(){
      $(".siderbar_menu li").toggleClass("sell");
    });

    $(".report").on('click',function(){
      $(".siderbar_menu li").toggleClass("reports");
    });

    $(".hamburger").click(function(){
      $(".wrapper").addClass("active");
    });

    $(".bg_shadow").click(function(){
      $(".wrapper").removeClass("active");
    });
  });