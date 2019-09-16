<?php
namespace Omnipay\Gestpay\Message;

use Omnipay\Gestpay\Message\AbstractRequest;
use Omnipay\Gestpay\Message\PurchaseResponse;
use Omnipay\Common\Message\ResponseInterface;


class PurchaseRequest extends AbstractRequest
{
	public function getData(){
		$this->validate('shopLogin', 'amount', 'uicCode', 'shopTransactionId');
		
		$data = array(
				'buyerName' => $this->getFulltName() ,
				'buyerEmail' => $this->getEmail(),
				'languageId' => $this->getLanguage(),
				'amount' => $this->getAmount(),
				'shopTransactionId' => $this->getTransactionId(),
				'uicCode' => $this->getCurrency(),
		);
		return $data = [
				'a' =>$this->getShopLogin(),
				'b' =>$this->Encrypt($data),
		];
	}
    public function sendData($data)
    {
    	return $this->response = new PurchaseResponse($this, $data);
    }
}
