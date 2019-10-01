<!DOCTYPE html>
<html lang="tw">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>ECPay站內付全方位金流範例程式</title>

	<style type="text/css">

		/*按鈕樣式定義*/
		.pay_button {
			text-decoration: none;
			color: #ffffff;
			min-width: 150px;
			display: inline-block;
			padding: 10px 20px;
			border-radius: 5px;
			letter-spacing: 2px;
			margin: 15px 0;
			background-color: #3f3f3f;
			background-image: -webkit-gradient(linear, left top, left bottom, from(#3f3f3f), to(#000000));
			background-image: -webkit-linear-gradient(top, #3f3f3f, #000000);
			background-image:-moz-linear-gradient(top, #3f3f3f, #000000);
			background-image:-ms-linear-gradient(top, #3f3f3f, #000000);
			background-image:-o-linear-gradient(top, #3f3f3f, #000000);
			background-image:linear-gradient(top bottom, #3f3f3f, #000000);
		}

	</style>

	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script type="text/javascript">
		<!--

		// 監聽API回傳訊息
		$(function () {
			window.addEventListener('message', function (e) {
				console.log('API回傳前端訂單資訊：'+e.data);
			});

			$( "#dialog" ).on( "dialogclose", function( event, ui ) {
				// 顯示付款按鈕
				$(".pay_button").fadeIn( "slow" );
			} );
		});


		// 檢查裝置類型
		function getIsMobileAgent () {
			var IsMobileAgent = false;
			var userAgent = navigator.userAgent;
			var CheckMobile = new RegExp("android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino");
			var CheckMobile2 = new RegExp("mobile|mobi|nokia|samsung|sonyericsson|mot|blackberry|lg|htc|j2me|ucweb|opera mini|mobi|android|iphone");

			if (CheckMobile.test(userAgent) || CheckMobile2.test(userAgent.toLowerCase())) {
				IsMobileAgent = true;
			}

			return IsMobileAgent
		}

		// 送出訂單
		function ajax_payment(payment_type, invoice_status)
		{

			// 隱藏付款按鈕
			$(".pay_button").fadeOut( "slow" );

			// 檢查裝置類型
			IsMobileAgent = getIsMobileAgent();

			// 送出AJAX產生訂單，並取得SPToken等資訊
			$.ajax({
				type: 'POST',
				url: 'process',
				dataType: 'json',
				data: 'func=pay&payment_type='+payment_type+'&invoice_status='+invoice_status,
				success: function (sMsg){
					if(sMsg.RtnCode == 1)
					{
						if(IsMobileAgent)
						{
							$( "#dialog" ).html('<div><img src="https://www.ecpay.com.tw/Content/Themes/WebStyle20131201/images/header_logo.png" height="40" style="display:block; margin:auto;"></div><iframe src="'+sMsg.SPCheckOut+'?MerchantID=' + sMsg.MerchantID + '&SPToken=' + sMsg.SPToken + '&PaymentType=' + sMsg.PaymentType + '"   frameborder="0" height="100%" width="100%" ></iframe>');

							$( ".dialog" ).dialog({
								resizable: false,
								modal: true
							});
						}
						else
						{
							$( "#dialog" ).html('<div><img src="https://www.ecpay.com.tw/Content/Themes/WebStyle20131201/images/header_logo.png" height="40" style="display:block; margin:auto;"></div><iframe src="'+sMsg.SPCheckOut+'?MerchantID=' + sMsg.MerchantID + '&SPToken=' + sMsg.SPToken + '&PaymentType=' + sMsg.PaymentType + '"   frameborder="0" height="90%" width="99%" ></iframe>');

							$( ".dialog" ).dialog({
								height: 700,
								width: 750,
								resizable: false,
								modal: true
							});
						}
					}
					else
					{
						console.log(sMsg.msg);
					}
				},
				error: function (sMsg1, sMsg2){
					$('.ajax-content').html('Ajax Error');
				}
			});
		}
		-->
	</script>
</head>

<body>
<button type="button" class="pay_button" onclick="ajax_payment('CREDIT', 0)">信用卡付款</button>
<button type="button" class="pay_button" onclick="ajax_payment('CREDIT', 1)">信用卡付款(觸發電子發票)</button>
<button type="button" class="pay_button" onclick="ajax_payment('ATM', 0)">ATM付款</button>
<button type="button" class="pay_button" onclick="ajax_payment('CVS', 0)">超商代碼付款</button>
<div id="dialog" class="dialog" title="ECPay站內付測試"></div>
</body>

</html>
