<?php

require_once BASE . '/lib/PEAR.php';

/**
 * PEAR_Error 例外 
 */
class PearException extends Exception
{
	/** @var PEAR_Error */
	protected $pearError;
	
	public function __construct(PEAR_Error $error)
	{
		$this->pearError = $error;
//		parent::__construct($error->getMessage(), $error->getCode());
		echo '<xmp>'; var_dump($error->userinfo); echo '</xmp>';
		parent::__construct($error->userinfo, $error->getCode());
	}
	
	/**
	 * 内包する PEAR_Error オブジェクトを取得
	 * 
	 * @return PEAR_Error 
	 */
	public function getPearError()
	{
		return $this->pearError;
	}
}
