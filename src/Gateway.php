<?php

namespace Omnipay\Sermepa;

use Symfony\Component\HttpFoundation\Request;
use Omnipay\Common\AbstractGateway;
use Omnipay\Sermepa\Message\CallbackResponse;

/**
 * Sermepa (Redsys) Gateway
 *
 * @author Javier Sampedro <jsampedro77@gmail.com>
 * @author NitsNets Studio <github@nitsnets.com>
 */
class Gateway extends AbstractGateway
{
    public function getDefaultParameters()
    {
        return [
            'titular' => '',
            'consumerLanguage' => '001',
            'currency' => 'EUR',
            'terminal' => '001',
            'merchantURL' => '',
            'merchantName' => '',
            'transactionType' => '0',
            'signatureMode' => 'simple',
            'testMode' => false
        ];
    }

    public function setMerchantName($merchantName)
    {
        $this->setParameter('merchantName', $merchantName);
        $this->setParameter('titular', $merchantName); //is this right??
    }

    public function setMerchantKey($merchantKey)
    {
        $this->setParameter('merchantKey', $merchantKey);
    }

    public function setMerchantCode($merchantCode)
    {
        $this->setParameter('merchantCode', $merchantCode);
    }

    public function setMerchantURL($merchantURL)
    {
        $this->setParameter('merchantURL', $merchantURL);
    }

    public function setTerminal($terminal)
    {
        $this->setParameter('terminal', $terminal);
    }

    public function setSignatureMode($signatureMode)
    {
        $this->setParameter('signatureMode', $signatureMode);
    }

    public function setConsumerLanguage($consumerLanguage)
    {
        $this->setParameter('consumerLanguage', $consumerLanguage);
    }

    public function setReturnUrl($returnUrl)
    {
        $this->setParameter('returnUrl', $returnUrl);
    }

    public function setCancelUrl($cancelUrl)
    {
        $this->setParameter('cancelUrl', $cancelUrl);
    }
    
    public function setCurrencyMerchant($currency)
    {
        $this->setParameter('merchantCurrency', $currency);
    }

    /**
     * Sets the identifier parameter. This parameter is used to flag in our request that we want a token back or to
     * send our token.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->setParameter('identifier', $identifier);
    }

    public function getName()
    {
        return 'Sermepa';
    }

    public function purchase(array $parameters = array())
    {
        if (isset($parameters['recurrent']) && $parameters['recurrent']) {
            return $this->createRequest('\Omnipay\Sermepa\Message\RecurrentPurchaseRequest', $parameters);
        } else {
            return $this->createRequest('\Omnipay\Sermepa\Message\PurchaseRequest', $parameters);
        }
    }
    
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Sermepa\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $returnObject
     * @return bool|CallbackResponse
     * @throws Exception\BadSignatureException
     * @throws Exception\CallbackException
     */
    public function checkCallbackResponse(Request $request, $returnObject = false)
    {
        $response = new CallbackResponse($request, $this->getParameter('merchantKey'));

        if ($returnObject) {
            return $response;
        }

        return $response->isSuccessful();
    }

    public function decodeCallbackResponse(Request $request)
    {
        return json_decode(base64_decode(strtr($request->get('Ds_MerchantParameters'), '-_', '+/')), true);
    }
}
