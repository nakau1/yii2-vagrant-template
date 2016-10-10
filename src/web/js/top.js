//ドーナツチャート　設定
$( function() {
    // オプション
    var options = {
        segmentShowStroke : false,
        percentageInnerCutout : 72,
        animation : true,
        animationSteps : 60,
        animationEasing : "easeInOutSine",
        animateRotate : true,
        animateScale : false,
        onAnimationComplete : null
    }
    var chart = new Chart(document.getElementById("canvas").getContext("2d")).Doughnut(doughnutData, options);
});

//サイト追加ボタンスライダー
$( function() {
	  $('.multiple-items').slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1
    });
});

//サイト追加ボタン 画像アスペクト比
$( function() {
    $('.site_rogo_img').imgLiquid({ fill: false });
});
