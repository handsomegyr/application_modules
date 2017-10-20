<?php
/**
 * ALIPAY API: alipay.ins.data.dsb.image.upload request
 *
 * @author auto create
 * @since 1.0, 2017-08-07 17:31:01
 */
class AlipayInsDataDsbImageUploadRequest
{
	/** 
	 * 保险公司定损单号，唯一标识一个定损单的id
	 **/
	private $estimateNo;
	
	/** 
	 * 车架号
	 **/
	private $frameNo;
	
	/** 
	 * 图片二进制字节流
	 **/
	private $imageContent;
	
	/** 
	 * 图像格式类型，目前仅支持jpg格式
	 **/
	private $imageFormat;
	
	/** 
	 * 图像文件名称
	 **/
	private $imageName;
	
	/** 
	 * 图片类型，比如：car_damage(车损图像)、certificate(证照图像)、material(资料图像)、other(其他图像)
	 **/
	private $imageType;
	
	/** 
	 * 车牌号
	 **/
	private $licenseNo;
	
	/** 
	 * 照片拍摄时间，精确到秒，格式yyyyMMddHHmmss
	 **/
	private $shootTime;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setEstimateNo($estimateNo)
	{
		$this->estimateNo = $estimateNo;
		$this->apiParas["estimate_no"] = $estimateNo;
	}

	public function getEstimateNo()
	{
		return $this->estimateNo;
	}

	public function setFrameNo($frameNo)
	{
		$this->frameNo = $frameNo;
		$this->apiParas["frame_no"] = $frameNo;
	}

	public function getFrameNo()
	{
		return $this->frameNo;
	}

	public function setImageContent($imageContent)
	{
		$this->imageContent = $imageContent;
		$this->apiParas["image_content"] = $imageContent;
	}

	public function getImageContent()
	{
		return $this->imageContent;
	}

	public function setImageFormat($imageFormat)
	{
		$this->imageFormat = $imageFormat;
		$this->apiParas["image_format"] = $imageFormat;
	}

	public function getImageFormat()
	{
		return $this->imageFormat;
	}

	public function setImageName($imageName)
	{
		$this->imageName = $imageName;
		$this->apiParas["image_name"] = $imageName;
	}

	public function getImageName()
	{
		return $this->imageName;
	}

	public function setImageType($imageType)
	{
		$this->imageType = $imageType;
		$this->apiParas["image_type"] = $imageType;
	}

	public function getImageType()
	{
		return $this->imageType;
	}

	public function setLicenseNo($licenseNo)
	{
		$this->licenseNo = $licenseNo;
		$this->apiParas["license_no"] = $licenseNo;
	}

	public function getLicenseNo()
	{
		return $this->licenseNo;
	}

	public function setShootTime($shootTime)
	{
		$this->shootTime = $shootTime;
		$this->apiParas["shoot_time"] = $shootTime;
	}

	public function getShootTime()
	{
		return $this->shootTime;
	}

	public function getApiMethodName()
	{
		return "alipay.ins.data.dsb.image.upload";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

     $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
    return $this->needEncrypt;
  }

}
