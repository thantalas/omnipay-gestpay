<?php
namespace Omnipay\Gestpay\Message;

use Omnipay\Gestpay\Message\AbstractRequest;
use Omnipay\Gestpay\Message\CompletePurchaseResponse;
use Omnipay\Common\Message\ResponseInterface;


class CompletePurchaseRequest extends AbstractRequest
{
	
	public function setA($value)
	{
		return $this->setParameter('a', $value);
	}
	public function getA()
	{
		return $this->getParameter('a');
	}
	public function setB($value)
	{
		return $this->setParameter('b', $value);
	}
	public function getB()
	{
		return $this->getParameter('b');
	}
	
	public function getData(){
		return $data = [
				'a' =>$this->getShopLogin(),
				'b' =>$this->Decrypt($this->getB()),
		];
	}
	
	public function sendData($data)
	{
		return $this->response = new Response($this, $data);
	}
}
