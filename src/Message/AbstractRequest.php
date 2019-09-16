<?php

namespace Omnipay\Gestpay\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Abstract Request
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
	protected $liveEndpoint = 'https://ecommS2S.sella.it/gestpay/GestPayWS/WsCryptDecrypt.asmx?wsdl';
	protected $testEndpoint = 'https://sandbox.gestpay.net/gestpay/GestPayWS/WsCryptDecrypt.asmx?wsdl';
	

	protected $_currencyMap = array(
			'EUR' => 242,
			'USD' => 1,
			'GBP' => 2,
			'CHF' => 3,
	);
	protected $_defLocale = '1';
	/*
	 * https://api.gestpay.it/#currency-codes
	 */
	protected $_langMap = array(
			'it' => 1,
			'en' => 2,
			'es' => 3,
			'fr' => 4,
			'de' => 5,
	);
	
	public function getLanguage(){
		return (isset($this->_langMap[$this->getLocale()])) ? $this->_langMap[$this->getLocale()] : $this->_langMap[$this->_defLocale];
	}
	
	/**
	 * http://api.gestpay.it/#encrypt
	 *
	 * @param array $data
	 *
	 * @return string Encrypted XML string
	 */
	public function Encrypt($data = [])
	{
		$xml_data = '';
		$data = array_merge([
				'shopLogin' => $this->getShopLogin(), 
				'uicCode' => $this->getCurrency()
			], 
				$data
		);
		foreach ($data as $key => $value) {
			$xml_data.= '<'.$key.'>'.$value.'</'.$key.'>';
		}
		$xml = file_get_contents( dirname(__FILE__) . '/../xml/encrypt.xml');
		$xml = str_replace('{request}', $xml_data, $xml);
		return $this->call($xml, 'Encrypt');
	}
	
	/**
	 * Decrypt SOAP response
	 * http://api.gestpay.it/#decrypt
	 *
	 * @param string $CryptedString The SOAP response crypted string
	 *
	 * @return string XML SOAP API call
	 */
	public function Decrypt($CryptedString)
	{
		$xml_data = '';
		$data = ['shopLogin' => $this->getShopLogin(), 'CryptedString' => $CryptedString];
		foreach ($data as $key => $value) {
			$xml_data.= '<'.$key.'>'.$value.'</'.$key.'>';
		}
		$xml = file_get_contents( dirname(__FILE__) . '/../xml/decrypt.xml');
		$xml = str_replace('{request}', $xml_data, $xml);
		$res= $this->call($xml, 'Decrypt');
		
		return $res;
	}
	
	public function call($xml, $op = 'Encrypt')
	{
		$header = array(
				"Content-type: text/xml; charset=utf-8\"",
				"Accept: text/xml",
				"Content-length: ".strlen($xml),
				"SOAPAction: \"https://ecomm.sella.it/".$op."\"",
		);
	
		$api_url = $this->getEndpoint();
		$soap = curl_init();
		curl_setopt($soap, CURLOPT_URL, $api_url );
		curl_setopt($soap, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($soap, CURLOPT_TIMEOUT,        10);
		curl_setopt($soap, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($soap, CURLOPT_POST,           true );
		curl_setopt($soap, CURLOPT_POSTFIELDS,     $xml);
		curl_setopt($soap, CURLOPT_HTTPHEADER,     $header);
		$xml_res = curl_exec($soap);
		
		curl_close($soap);
		return $xml_res;
	}
	/**
	 * Get the endpoint where the request should be made.
	 *
	 * @return string the URL of the endpoint
	 */
	public function getEndpoint()
	{
		return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
	}


	public function getShopLogin()
	{
		return $this->getParameter('shopLogin');
	}

	public function setShopLogin($value)
	{
		return $this->setParameter('shopLogin', $value);
	}

	public function setAmount($value)
	{
		return $this->setParameter('amount', $value);
	}
	public function getAmount()
	{
		return $this->getTestMode() ? 0.01 : $this->getParameter('amount');
	}

	public function setTransactionId($value)
	{
		return $this->setParameter('shopTransactionId', $value);
	}
	public function getTransactionId()
	{
		return $this->getParameter('shopTransactionId');
	}
	
	
	public function setCurrency($value)
	{
		return $this->setParameter('uicCode', $value);
	}
	public function getCurrency()
	{
		$c = $this->getParameter('uicCode');
		
		return isset($this->_currencyMap[$c]) ?  $this->_currencyMap[$c] : $this->_currencyMap['EUR'];
	}
	

	public function setEmail($value)
	{
		return $this->setParameter('email', $value);
	}
	public function getEmail()
	{
		return $this->getParameter('email');
	}
	
	public function setFirstName($value)
	{
		return $this->setParameter('firstName', $value);
	}
	public function getFirstName()
	{
		return $this->getParameter('firstName');
	}
	
	public function setLastName($value)
	{
		return $this->setParameter('lastName', $value);
	}
	public function getLastName()
	{
		return $this->getParameter('lastName');
	}

	public function getFulltName()
	{
		return $this->getFirstName() . " " . $this->getLastName();
	}
	
	public function getHttpRequest()
	{
		return $this->httpRequest;
	}
	/**
	 * Get HTTP Method.
	 *
	 * This is nearly always POST but can be over-ridden in sub classes.
	 *
	 * @return string the HTTP method
	 */
	public function getHttpMethod()
	{
		return 'POST';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function sendData($data)
	{
		$response = $this->httpClient->request(
				$this->getHttpMethod(),
				$this->getEndpoint(),
				$this->getHeaders(),
				json_encode($data)
				);
		
		$payload =  json_decode($response->getBody()->getContents(), true);
		
		return $this->createResponse($payload);
	}
	
	public function createResponse($data)
	{
		return $this->response = new Response($this, $data);
	}
	
	public function getHeaders()
	{
		return [];
	}
	
	public function setLocale($value)
	{
		return $this->setParameter('langID', $value);
	}
	
	/**
	 *  1	Italian
		2	English
		3	Spanish
		4	Franch
		5	German
	 * @return string
	 */
	public function getLocale()
	{
		return strtolower($this->getParameter('langID'));
	}


}