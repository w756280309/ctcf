<?php
namespace org_mapu_themis_rop_model;
use org_mapu_themis_rop_model\HashBasedPreservationCreateRequest as HashBasedPreservationCreateRequest;
/**
 * 客户端请求体
 * 凭证文件保全
 * @edit yfx 2015.06.09
 */
class VoucherHashPreservationCreateRequest extends HashBasedPreservationCreateRequest{
	static $v="1.0";
	static $method="voucher.hash.preservation.create";
	
	
	/**
	 * 凭证号 (必填)
	 */
	public $voucherNumber;
	
	/**
	 * 交易流水号(必填)
	 */
	public $transactionNumber;
	
	/**
	 * 金额(必填)
	 */
	public $transactionAmount;
	
	/**
	 * 币种代码（可选）  - 默认是人民币RMB
	 */
	public $currencyCode;
	
	/**
	 * 凭证类型(必填) - 枚举值
	 */
	public $voucherType;
	
	/**
	 * 保全主体交易角色 (必填)   0 - 付款方  , 1 - 收款方
	 */
	public $transactionEntityType;
	
	/**
	 * 账户名(付款方)（可选）
	 */
	public $creditorAccountName;
	
	/**
	 * 账户号(付款方)（可选）
	 */
	public $creditorAccountNumber;
	
	/**
	 * 账户类型(付款方)（可选）
	 */
	public $creditorAccountType;
	
	/**
	 * 账户名(收款方)（可选）
	 */
	public $debtorAccountName;
	
	/**
	 * 账户号(收款方)（可选）
	 */
	public $debtorAccountNumber;
	
	/**
	 * 账户类型(收款方)（可选）
	 */
	public $debtorAccountType;
	
	/**
	 * 交易时间（必填）
	 */
	public $transactionTime;
	
	/**
	 * 交易备注（可选）
	 */
	public $comments;
	
	
	//请求的版本号
	function getVersion(){
		return self::$v;
	}
	
	//请求的方法
	function getMethod(){
		return self::$method;
	}
}
?>
