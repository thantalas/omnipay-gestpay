<?php
namespace Omnipay\Gestpay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Gestpay\Message\Response;
/**
 * Dummy Response
 *
 * This is the response class for all gestpay purchase requests.
 *
 * @see \Omnipay\Gestpay\Gateway
 */
class PurchaseResponse extends Response
{
	/**
	 * PaymentPage Official URI
	 */
	protected $paymentLiveUrl = 'https://ecomm.sella.it/pagam/pagam.aspx';
	
	/**
	 * PaymentPage TEST URI
	 */
	protected $paymentTestUrl = 'https://testecomm.sella.it/pagam/pagam.aspx';
	
	
	protected function _getRiderectUrl(){
		
		return $this->getRequest()->getTestMode() ? $this->paymentTestUrl : $this->paymentLiveUrl;
	}
	
    public function isSuccessful()
    {
    	return false;
    }

    public function isRedirect()
    {
        return true;
    }
    public function getRedirectUrl()
    {
    	$this->normaliseResponse();

    	if ( false !== $this->GestPayResponse->getTransactionResult()) {
    		
    		$ResPreg = preg_match('/<CryptDecryptString>([^<]+)<\/CryptDecryptString>/', $this->data['b'], $match) ;
    		$data = [
    			'a' => $this->data['a'],
    			'b' => $this->GestPayResponse->getCriptedString(),
    		];
    		
    		return $this->_getRiderectUrl() . '?' . http_build_query($data);
    	}
    	return false;
    }

}
