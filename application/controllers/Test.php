<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH . "libraries/ECPayAIO_PHP/AioSDK/sdk/ECPay.Payment.Integration.php");

class Test extends CI_Controller
{

	public $ReturnURL,$ServiceURL,$HashKey,$HashIV,$MerchantID;
	public function landing()
	{
		$this->load->view('landing');
	}

	public function __construct()
	{
		parent::__construct();
		$this->ReturnURL = "test/redirect"; //original "http://www.ecpay.com.tw/receive.php";

		/* from flemlin https://github.com/flamelin/ECPay
		 return [
					'ServiceURL' => env('PAY_SERVICE_URL', 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V2'),
					'HashKey' => env('PAY_HASH_KEY', '5294y06JbISpM5x9'),
					'HashIV' => env('PAY_HASH_IV', 'v77hoKGq4kWxNNIS'),
					'MerchantID' => env('PAY_MERCHANT_ID', '2000132'),
				];
		*/

		$this->ServiceURL = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V2"; //original https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5
		$this->HashKey = "5294y06JbISpM5x9"; //original 5294y06JbISpM5x9
		$this->HashIV = "v77hoKGq4kWxNNIS"; //original v77hoKGq4kWxNNIS
		$this->MerchantID = "2000214"; //original 2000132 //not otp 2000214
	}

	public function atm()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL = $this->ServiceURL;   //服務位置
			$obj->HashKey = $this->HashKey;                                           //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                           //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                                     //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                           //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test" . time();
			$obj->Send['ReturnURL'] = $this->ReturnURL;    //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                          //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       //交易時間
			$obj->Send['TotalAmount'] = 2000;                                      //交易金額
			$obj->Send['TradeDesc'] = "good to drink";                          //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::ATM;                 //付款方式:ATM
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int)"1", 'URL' => "dedwed"));
			//ATM 延伸參數(可依系統需求選擇是否代入)
			$obj->SendExtend['ExpireDate'] = 3;     //繳費期限 (預設3天，最長60天，最短1天)
			$obj->SendExtend['PaymentInfoURL'] = ""; //伺服器端回傳付款相關資訊。
			# 電子發票參數
			/*
           $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
           $obj->SendExtend['RelateNumber'] = "Test".time();
           $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
           $obj->SendExtend['CustomerPhone'] = '0911222333';
           $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
           $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
           $obj->SendExtend['InvoiceItems'] = array();
           // 將商品加入電子發票商品列表陣列
           foreach ($obj->Send['Items'] as $info)
           {
               array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                   $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
           }
           $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
           $obj->SendExtend['DelayDay'] = '0';
           $obj->SendExtend['InvType'] = ECPay_InvType::General;
           */
			//產生訂單(auto submit至ECPay)
			$html = $obj->CheckOut();
			echo $html;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function all()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL = $this->ServiceURL;  //服務位置
			$obj->HashKey = $this->HashKey;                                          //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                          //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                                    //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                          //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test" . time();
			$obj->Send['ReturnURL'] = $this->ReturnURL;     //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                           //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
			$obj->Send['TotalAmount'] = 2000;                                       //交易金額
			$obj->Send['TradeDesc'] = "good to drink";                           //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::ALL;                  //付款方式:全功能
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int)"1", 'URL' => "dedwed"));
			# 電子發票參數
			/*
            $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = "Test".time();
            $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
            $obj->SendExtend['InvoiceItems'] = array();
            // 將商品加入電子發票商品列表陣列
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = ECPay_InvType::General;
            */
			//產生訂單(auto submit至ECPay)
			$obj->CheckOut();


		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function barcode()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL = $this->ServiceURL;   //服務位置
			$obj->HashKey = $this->HashKey;                                           //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                           //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                                     //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                           //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test" . time();
			$obj->Send['ReturnURL'] = $this->ReturnURL;    //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                          //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       //交易時間
			$obj->Send['TotalAmount'] = 2000;                                      //交易金額
			$obj->Send['TradeDesc'] = "good to drink";                          //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::BARCODE;             //付款方式:BARCODE超商代碼
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int)"1", 'URL' => "dedwed"));
			//BARCODE超商條碼延伸參數(可依系統需求選擇是否代入)
			$obj->SendExtend['Desc_1'] = '';      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['Desc_2'] = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['Desc_3'] = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['Desc_4'] = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['PaymentInfoURL'] = '';      //預設空值
			$obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
			$obj->SendExtend['StoreExpireDate'] = '';      //預設空值
			# 電子發票參數
			/*
            $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = "Test".time();
            $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
            $obj->SendExtend['InvoiceItems'] = array();
            // 將商品加入電子發票商品列表陣列
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = ECPay_InvType::General;
            */
			//產生訂單(auto submit至ECPay)
			$obj->CheckOut();

		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}

	public function credit()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL = $this->ServiceURL;   //服務位置
			$obj->HashKey = $this->HashKey;                                           //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                           //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                                     //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                           //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test" . time();
			$obj->Send['ReturnURL'] = $this->ReturnURL;    //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                          //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       //交易時間
			$obj->Send['TotalAmount'] = 2000;                                      //交易金額
			$obj->Send['TradeDesc'] = "good to drink";                          //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::Credit;              //付款方式:Credit
			$obj->Send['IgnorePayment'] = ECPay_PaymentMethod::GooglePay;           //不使用付款方式:GooglePay
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int)"1", 'URL' => "dedwed"));
			//Credit信用卡分期付款延伸參數(可依系統需求選擇是否代入)
			//以下參數不可以跟信用卡定期定額參數一起設定
			$obj->SendExtend['CreditInstallment'] = '';    //分期期數，預設0(不分期)，信用卡分期可用參數為:3,6,12,18,24

			$obj->SendExtend['Redeem'] = false;           //是否使用紅利折抵，預設false
			$obj->SendExtend['UnionPay'] = false;          //是否為聯營卡，預設false;
			//Credit信用卡定期定額付款延伸參數(可依系統需求選擇是否代入)
			//以下參數不可以跟信用卡分期付款參數一起設定
			// $obj->SendExtend['PeriodAmount'] = '' ;    //每次授權金額，預設空字串
			// $obj->SendExtend['PeriodType']   = '' ;    //週期種類，預設空字串
			// $obj->SendExtend['Frequency']    = '' ;    //執行頻率，預設空字串
			// $obj->SendExtend['ExecTimes']    = '' ;    //執行次數，預設空字串

			# 電子發票參數
			/*
            $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = "Test".time();
            $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
            $obj->SendExtend['InvoiceItems'] = array();
            // 將商品加入電子發票商品列表陣列
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = ECPay_InvType::General;
            */
			//產生訂單(auto submit至ECPay)
			$obj->CheckOut();

		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}

	public function cvs()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL = $this->ServiceURL;    //服務位置
			$obj->HashKey = $this->HashKey;                                            //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                            //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                                      //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                            //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test" . time();
			$obj->Send['ReturnURL'] = $this->ReturnURL;     //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                           //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
			$obj->Send['TotalAmount'] = 2000;                                       //交易金額
			$obj->Send['TradeDesc'] = "good to drink";                           //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::CVS;                  //付款方式:CVS超商代碼
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int)"1", 'URL' => "dedwed"));
			//CVS超商代碼延伸參數(可依系統需求選擇是否代入)
			$obj->SendExtend['Desc_1'] = '';      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['Desc_2'] = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['Desc_3'] = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['Desc_4'] = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
			$obj->SendExtend['PaymentInfoURL'] = '';      //預設空值
			$obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
			$obj->SendExtend['StoreExpireDate'] = '';      //預設空值
			# 電子發票參數
			/*
            $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = "Test".time();
            $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
            $obj->SendExtend['InvoiceItems'] = array();
            // 將商品加入電子發票商品列表陣列
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = ECPay_InvType::General;
            */
			//產生訂單(auto submit至ECPay)
			$obj->CheckOut();

		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}

	public function googlepay()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL = $this->ServiceURL;  //服務位置
			$obj->HashKey = $this->HashKey;                                          //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV = $this->HashIV;                                          //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID = $this->MerchantID;                                                    //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                          //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test" . time();
			$obj->Send['ReturnURL'] = $this->ReturnURL;     //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                           //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
			$obj->Send['TotalAmount'] = 2000;                                       //交易金額
			$obj->Send['TradeDesc'] = "good to drink";                           //交易描述
			$obj->Send['ChoosePayment'] = ECPay_PaymentMethod::GooglePay;           //付款方式:GooglePay
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int)"1", 'URL' => "dedwed"));
			# 電子發票參數
			/*
            $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = "Test".time();
            $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
            $obj->SendExtend['InvoiceItems'] = array();
            // 將商品加入電子發票商品列表陣列
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = ECPay_InvType::General;
            */
			//產生訂單(auto submit至ECPay)
			$obj->CheckOut();


		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function web_atm()
	{
		try {

			$obj = new ECPay_AllInOne();

			//服務參數
			$obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5"; //服務位置
			$obj->HashKey     = '5294y06JbISpM5x9' ;                                         //測試用Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV      = 'v77hoKGq4kWxNNIS' ;                                         //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID  = '2000132';                                                   //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                         //CheckMacValue加密類型，請固定填入1，使用SHA256加密
			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = "Test".time() ;
			$obj->Send['ReturnURL']         = ReturnURL ;     //付款完成通知回傳的網址
			$obj->Send['MerchantTradeNo']   = $MerchantTradeNo;                           //訂單編號
			$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
			$obj->Send['TotalAmount']       = 2000;                                       //交易金額
			$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
			$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::WebATM ;               //付款方式:WebATM
			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
				'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));
			# 電子發票參數
			/*
           $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
           $obj->SendExtend['RelateNumber'] = "Test".time();
           $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
           $obj->SendExtend['CustomerPhone'] = '0911222333';
           $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
           $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
           $obj->SendExtend['InvoiceItems'] = array();
           // 將商品加入電子發票商品列表陣列
           foreach ($obj->Send['Items'] as $info)
           {
               array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                   $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
           }
           $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
           $obj->SendExtend['DelayDay'] = '0';
           $obj->SendExtend['InvType'] = ECPay_InvType::General;
           */
			//產生訂單(auto submit至ECPay)
			//$obj->CheckOut();
			echo $obj->CheckOut();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function server_reply()
	{
		try {
			// 收到綠界科技的付款結果訊息，並判斷檢查碼是否相符
			$AL = new ECPay_AllInOne();
			$AL->MerchantID =  $this->MerchantID;
			$AL->HashKey = '5294y06JbISpM5x9';
			$AL->HashIV = 'v77hoKGq4kWxNNIS';
			// $AL->EncryptType = ECPay_EncryptType::ENC_MD5;  // MD5
			$AL->EncryptType = ECPay_EncryptType::ENC_SHA256; // SHA256
			$feedback = $AL->CheckOutFeedback();

			var_dump($feedback);

			// 以付款結果訊息進行相對應的處理
			/**
			 * 回傳的綠界科技的付款結果訊息如下:
			 * Array
			 * (
			 * [MerchantID] =>
			 * [MerchantTradeNo] =>
			 * [StoreID] =>
			 * [RtnCode] =>
			 * [RtnMsg] =>
			 * [TradeNo] =>
			 * [TradeAmt] =>
			 * [PaymentDate] =>
			 * [PaymentType] =>
			 * [PaymentTypeChargeFee] =>
			 * [TradeDate] =>
			 * [SimulatePaid] =>
			 * [CustomField1] =>
			 * [CustomField2] =>
			 * [CustomField3] =>
			 * [CustomField4] =>
			 * [CheckMacValue] =>
			 * )
			 */
			// 在網頁端回應 1|OK
			echo '1|OK';
		} catch (Exception $e) {
			echo '0|' . $e->getMessage();
		}
	}

	public function redirect()
	{
		echo "<pre>";
		print_r($_POST);
		echo "<pre>";
	}


}
