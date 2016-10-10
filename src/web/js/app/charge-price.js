$(function () {

    //  fancybox - jquery plugin
    //  http://fancybox.net/
    //  http://fancyapps.com/fancybox/
    $("#confirm-trigger").fancybox();

    // DOMs
    var cardIssueFeeDom        = $("input[name='card-issue-fee']");
    var pointSiteCodeDom       = $("input[name='point-site-code']");
    var priceInputDom          = $("#chargepriceform-price");
    var pointTotalPriceDom     = $("#point-total-price");
    var errorDom               = $("div.help-block");
    var add100ButtonDom        = $("#add-100");
    var add1000ButtonDom       = $("#add-1000");
    var add5000ButtonDom       = $("#add-5000");
    var confirmChargeButtonDom = $("#confirm-charge-button");
    var commitChargeButtonDom  = $("#commit-charge-button");
    var confirmChargePriceDom  = $("#confirm-charge-price");

    /**
     * 入力された値が空かどうか
     * @returns {boolean}
     */
    function isEmptyPriceInput() {
        return (priceInputDom.val().length <= 0);
    }

    /**
     * 入力された値が整数かどうか
     * @returns {boolean}
     */
    function isNumberPriceInput() {
        return priceInputDom.val().match(/^([1-9]\d*|0)$/);
    }

    /**
     * 入力された値を整数で取得する
     * @returns {number}
     */
    function getInputtedPrice() {
        var inputted = 0;
        if (!isEmptyPriceInput() || isNumberPriceInput()) {
            inputted = parseInt(priceInputDom.val());
        }
        return inputted;
    }

    /**
     * ポイント交換総額を計算して表示を更新する
     */
    function changePointTotalPrice() {
        if (isEmptyPriceInput() || !isNumberPriceInput()) {
            pointTotalPriceDom.text("--");
            return;
        }

        var inputted = getInputtedPrice();
        var cardIssueFee = parseInt(cardIssueFeeDom.val());
        var pointTotalPrice = inputted + cardIssueFee;
        pointTotalPriceDom.text((pointTotalPrice).toLocaleString() + "円");
    }

    /**
     * 値に指定の金額を追加する
     * @param price 追加する金額
     */
    function addInputPrice(price) {
        priceInputDom.focus();
        priceInputDom.val(getInputtedPrice() + price);
        changePointTotalPrice();
    }

    // +100円ボタン押下時
    add100ButtonDom.click(function () {
        add100ButtonDom.focus();
        addInputPrice(100);
    });

    // +1000円ボタン押下時
    add1000ButtonDom.click(function () {
        add1000ButtonDom.focus();
        addInputPrice(1000);
    });

    // +5000円ボタン押下時
    add5000ButtonDom.click(function () {
        add5000ButtonDom.focus();
        addInputPrice(5000);
    });

    // 入力内容変更時
    priceInputDom.change(function () {
        changePointTotalPrice();
    });

    // 確認ボタン押下時
    confirmChargeButtonDom.click(function () {
        var error = errorDom.text();
        if (error.length > 0) {
            return false;
        }
    
        confirmChargePriceDom.text(getInputtedPrice().toLocaleString());
        
        $("#confirm-trigger").click();
    });

    // チャージするボタン(確定ボタン)押下時
    commitChargeButtonDom.click(function () {
        var params = {
            'point_site_code': pointSiteCodeDom.val(),
            'price':           getInputtedPrice()
        };

        $.ajax({
            type: "POST",
            url: "price-request",
            data: params,
            error: function (request) {
                console.log(request.responseText);
                alert("処理に失敗しました"); //TODO:失敗したときの処理は未定
            },
            success: function () {
                window.location.href = 'price-finished';
            }
        });
    });

    // ポイント交換総額の計算
    changePointTotalPrice();
});
