<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;

class TokenManager
{
   // generate csrf token
    public function generateCSRFtoken(): string
    {
        $session = new Session();
        $token = $session->get('csrf_token');
        
        if (!$token)
            $token = bin2hex(openssl_random_pseudo_bytes(32));

        $session->set('csrf_token', $token);

        return $token;
    }

    public function checkCSRFtoken($form_token)
    {
        if ($form_token === null)
            return 'CSRF Token is not valid';
                        
    	$session = new Session();
 		$session_token = $session->get('csrf_token');

 		if (hash_equals($session_token, $form_token))
 			return;
 		else
 			return 'CSRF Token is not valid';
    }	
}