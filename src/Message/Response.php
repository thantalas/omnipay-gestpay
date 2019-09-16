<?php
namespace Omnipay\Gestpay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Gestpay\Message\GestpayResponse;
/**
 * Dummy Response
 *
 * This is the response class for all Dummy requests.
 *
 * @see \Omnipay\\gestpay\Gateway
 */
class Response extends AbstractResponse
{
	protected $GestPayResponse = null;
	/**
	 * Decrypt SOAP response in order to checks whether the payment has been successful
	 *
	 * @return array $result containing 'transaction_result' (boolean true|false) and 'shop_transaction_id'
	 */
	public function normaliseResponse()
	{
		return $this->GestPayResponse = new GestpayResponse($this->data['b']);

	}
	
	public function getAllData(){
		$thi->normaliseResponse();
		return $this->GestPayResponse->toArray();
	}
    public function isSuccessful()
    {
    	$this->normaliseResponse();
    	return $this->GestPayResponse->getTransactionResult();
    }

    public function getTransactionReference()
    {
    	$this->normaliseResponse();
    	return $this->GestPayResponse->getBankTransactionId();
    	
    }

    public function getTransactionId()
    {
    	return $this->getTransactionReference();
    }
    public function getShopTransactionId()
    {
    	$this->normaliseResponse();
    	return $this->GestPayResponse->getShopTransactionId();
    }

    public function getMessage()
    {
    	$this->normaliseResponse();
    	return $this->GestPayResponse->getErrorDescription(); 
    }
 
}
