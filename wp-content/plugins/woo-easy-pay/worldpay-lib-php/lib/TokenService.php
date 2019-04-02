<?php
namespace Worldpay;

class TokenService
{
    public static function getStoredCardDetails($token)
    {
        return Connection::getInstance()->sendRequest('tokens/' . $token, false, true, 'GET');
    }
    
    /**
     * Delete the payment method token.
     * @param string $token
     */
    public static function deleteToken($token){
    	return Connection::getInstance()->sendRequest('tokens/' . $token, false, false, 'DELETE');
    }
}
