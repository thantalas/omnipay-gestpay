<?php
namespace Omnipay\gestpay;

use Omnipay\Common\AbstractGateway;

/**
 * gestpay Gateway
 *
 * http://api.gestpay.it/#introduction
 * 
 * https://docs.gestpay.it/test/test-credit-cards.html
 * 
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'gestpay';
    }
    
    public function capture(array $parameters = array())
    {
    	return $this->createRequest('\Omnipay\Gestpay\Message\TransactionReferenceRequest', $parameters);
    }
    
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Gestpay\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Gestpay\Message\CompletePurchaseRequest', $parameters);
    }
    
}
