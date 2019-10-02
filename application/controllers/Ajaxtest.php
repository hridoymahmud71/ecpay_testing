<?php

defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH . "libraries/ECPaySPCheckOut_PHP/ECPay.Payment.Integration.php");

class Ajaxtest extends CI_Controller
{
	public $ReturnURL,$ServiceURL, $sSPCheckOut_Url, $HashKey, $HashIV, $MerchantID;

	public function __construct()
	{
		parent::__construct();
		$this->ServiceURL = 'https://payment-stage.ecpay.com.tw/SP/CreateTrade'; //original https://payment-stage.ecpay.com.tw/SP/CreateTrade
		$this->sSPCheckOut_Url = "https://payment-stage.ecpay.com.tw/SP/SPCheckOut"; //original https://payment-stage.ecpay.com.tw/SP/SPCheckOut
		$this->HashKey = "5294y06JbISpM5x9"; //original 5294y06JbISpM5x9
		$this->HashIV = "v77hoKGq4kWxNNIS"; //original v77hoKGq4kWxNNIS
		$this->MerchantID = "2000214"; //original 2000132 //not otp 2000214
		$this->ReturnURL = $_SERVER['HTTP_HOST'] . '/ajaxtest/redirect';
	}

	public function landing()
	{
		$data = array();
		$this->load->view('ajax_landing', $data);
	}

	public function process()
	{
		$aShopping_Cart = array();  // 購物車內資訊
		$aOrder_Info = array();  // 訂單資訊
		$aAjax_Return = array();  // 回傳給前端頁面資訊
		$sSPCheckOut_Url = $this->sSPCheckOut_Url;                                                  // 付款連結
		$sPayment_Type = isset($_POST['payment_type']) ? htmlspecialchars(trim($_POST['payment_type'])) : 'CREDIT';      // 付款方式
		$nInvoice_Status = isset($_POST['invoice_status']) ? (int)$_POST['invoice_status'] : 1;                             // 開立發票


		/*------------------------*/
		$aOrder_Info = array();
		$aShopping_Cart = array();
		$aOrder_Info = $aShopping_Cart;
		$aOrder_Info['order_id'] = 'SDKTEST' . time();
		$aOrder_Info['order_amount'] = 5274;
		$aOrder_Info['Items'] = array('Name' => "義吸沛礦泉水", 'Price' => (int)"5274", 'Currency' => "元", 'Quantity' => (int)"1");
		/*-------------------------*/


		/*-----------------------------------------------------------------------*/
		try {

			$obj = new ECPay_AllInOne();
			//服務參數
			$obj->ServiceURL = $this->ServiceURL;    //服務位置
			$obj->HashKey = $this->HashKey;                                    //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                    //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                              //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                    //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$obj->Send['ReturnURL'] = $this->ReturnURL; //$_SERVER['HTTP_HOST'] . '/payment_receive.php';            //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $aOrder_Info['order_id'];                                 //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                                      //交易時間
			$obj->Send['TotalAmount'] = $aOrder_Info['order_amount'];                             //交易金額
			$obj->Send['TradeDesc'] = "Whosoever drinketh of this water shall thirst again";   //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::ALL;                                //付款方式:全功能
			$obj->Send['NeedExtraPaidInfo'] = 'Y';
			// //訂單的商品資料
			array_push($obj->Send['Items'], $aOrder_Info['Items']);
			if ($sPayment_Type == 'CREDIT') {
				$obj->SendExtend['Redeem'] = 'Yes';                     // 紅利折抵
			}
			if ($sPayment_Type == 'ATM') {
				// ATM 延伸參數
				$obj->SendExtend['ExpireDate'] = 1;                    //繳費期限 (預設3天，最長60天，最短1天)
				$obj->SendExtend['PaymentInfoURL'] = "";                //伺服器端回傳付款相關資訊。
			}

			if ($sPayment_Type == 'CVS') {
				// CVS超商代碼延伸參數(可依系統需求選擇是否代入)
				$obj->SendExtend['Desc_1'] = 'Desc_1';       //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
				$obj->SendExtend['Desc_2'] = 'Desc_2';       //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
				$obj->SendExtend['Desc_3'] = '';             //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
				$obj->SendExtend['Desc_4'] = '';             //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
				$obj->SendExtend['PaymentInfoURL'] = '';             //預設空值
				$obj->SendExtend['ClientRedirectURL'] = '';             //預設空值
				$obj->SendExtend['StoreExpireDate'] = '2';            //預設空值 (以分鐘為單位)
			}
			if ($nInvoice_Status == 1) {
				// 電子發票參數
				$obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
				$obj->SendExtend['RelateNumber'] = "SDKTEST" . time();
				$obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
				$obj->SendExtend['CustomerPhone'] = '0911222333';
				$obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
				$obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號6樓D棟';
				$obj->SendExtend['InvoiceItems'] = array();

				// 將商品加入電子發票商品列表陣列
				foreach ($obj->Send['Items'] as $info) {
					array_push($obj->SendExtend['InvoiceItems'], array('Name' => $info['Name'], 'Count' =>
						$info['Quantity'], 'Word' => '個', 'Price' => $info['Price'], 'TaxType' => ECPay_TaxType::Dutiable));
				}
				$obj->SendExtend['InvoiceRemark'] = '測試發票備註';
				$obj->SendExtend['DelayDay'] = '0';
				$obj->SendExtend['InvType'] = ECPay_InvType::General;
			}

			//產生訂單(auto submit至ECPay)
			$aSdk_Return = $obj->CreateTrade();
			// 接回來的參數
			//var_dump($aSdk_Return);
			// exit;
			$aSdk_Return['SPCheckOut'] = $sSPCheckOut_Url;
			if ($sPayment_Type == 'CREDIT') {
				$aSdk_Return['PaymentType'] = 'CREDIT';
			} elseif ($sPayment_Type == 'ATM') {
				$aSdk_Return['PaymentType'] = 'ATM';
			} elseif ($sPayment_Type == 'CVS') {
				$aSdk_Return['PaymentType'] = 'CVS';
			} else {
				$aSdk_Return['PaymentType'] = 'CREDIT';
			}

			$sAjax_Return = json_encode($aSdk_Return);
		} catch (Exception $e) {
			// var_dump($e->getMessage());
			// exit;
			$aAjax_Return['msg'] = $e->getMessage();
			$sAjax_Return = json_encode($aAjax_Return);
		}

		// 4.將API回傳參數往前端送
		if (!empty($sAjax_Return)) {

			//test
			/*echo "<pre>";
			print_r($sAjax_Return);
			echo "</pre>";
			die();*/

			echo $sAjax_Return;

			/*$output = print_r($aAjax_Return, true);
			file_put_contents(FCPATH.'dummy.txt', $output);*/

		}
		/*-----------------------------------------------------------------------*/
	}

	public function redirect()
	{
		echo "<pre>";
		print_r($_POST);
		echo "<pre>";
	}


}

