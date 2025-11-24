<?php

    class ConfigInfocert {
		public static $username = 'brain-computing';
        public static $mode_stage = false;
        
        // url stage
        public static $url_auth_stage = "https://idpstage.infocert.digital/auth/realms/delivery/protocol/openid-connect/token";
        public static $url_api_stage = "https://apistage.infocert.digital/signature/v1/";
		public static $password_stage = 'b26d265b-6348-494f-8d8b-d31101d67339';
        // url produzione
        public static $url_auth = "https://idp.infocert.digital/auth/realms/delivery/protocol/openid-connect/token";
        public static $url_api = "https://api.infocert.digital/signature/v1/";
        public static $password_prod = '76b3cf4b-887e-4470-bb65-aeb0b586a341';
    }

    function getOauthToken() {
        if (ConfigInfocert::$mode_stage) {
            $host = ConfigInfocert::$url_auth_stage;
			$password = ConfigInfocert::$password_stage;
        } else {
            $host = ConfigInfocert::$url_auth;
			$password = ConfigInfocert::$password_prod;
        }
        
        $payloadName = [
            'grant_type' => 'client_credentials',
            'scope' => 'signature'
        ];
        
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_USERPWD, ConfigInfocert::$username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloadName));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        
        if ($return === FALSE) {
            printf("41 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        }
        
        curl_close($ch);
        
        return json_decode($return);
    }
    function getCertificates($token, $x_signer_id) {
		echo($x_signer_id);
        if (ConfigInfocert::$mode_stage) {
            $host = ConfigInfocert::$url_api_stage;
        } else {
            $host = ConfigInfocert::$url_api;
        }
        
        $ch = curl_init($host . "certificates");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            "Authorization: Bearer " . $token,
            "X-signer-id: " . $x_signer_id
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        
        if ($return === FALSE) {
            printf("69 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        }
        
        curl_close($ch);
        
        return json_decode($return);
    }
    
    function getCertificate($token, $x_signer_id, $certificato_id) {
        if (ConfigInfocert::$mode_stage) {
            $host = ConfigInfocert::$url_api_stage;
        } else {
            $host = ConfigInfocert::$url_api;
        }
        
        $ch = curl_init($host . "certificates/" . $certificato_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: Bearer " . $token,
            "X-signer-id: " . $x_signer_id
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        
        if ($return === FALSE) {
            printf("97 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        }
        
        curl_close($ch);
        
        return json_decode($return);
    }
    
    function challenge($token, $x_signer_id) {
        if (ConfigInfocert::$mode_stage) {
            $host = ConfigInfocert::$url_api_stage;
        } else {
            $host = ConfigInfocert::$url_api;
        }

        $ch = curl_init($host . "authenticators/".$x_signer_id."/challenge/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: Bearer " . $token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);

        if ($return === FALSE) {
            printf("125 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        }
        
        curl_close($ch);
        
        return json_decode($return);
    }
    
    function sign_pades_doc($token, $x_signer_id, $pin, $otp, $transaction_id, $file, $doc_multifirma, $lato_firme, $num_firma_da_apporre, $rtf_landscape, $id_modulo) {
		if (ConfigInfocert::$mode_stage) {
		    $host = ConfigInfocert::$url_api_stage;
		} else {
		    $host = ConfigInfocert::$url_api;
		}

		// se monofirma, definisco la posizione del sigillo in alto al documento
		if($doc_multifirma == 'n') 
		{
			if($rtf_landscape == 0)
			{
				$llx = 350;			// llx(lower left x coordinate) = margin from left.
				$lly = 790;			// lly(lower left y coordinate) = margin from bottom
				$urx = 480;			// urx(upper right x coordinate) = margin from left bottom = width of rectangle
				$ury = 820;			// ury(upper right y coordinate) = margin from left bottom = height of rectangle
			}
			elseif($rtf_landscape == 1)
			{
				$llx = 400;			// llx(lower left x coordinate) = margin from left.
				$lly = 580;			// lly(lower left y coordinate) = margin from bottom
				$urx = 500;			// urx(upper right x coordinate) = margin from left bottom = width of rectangle
				$ury = 540;			// ury(upper right y coordinate) = margin from left bottom = height of rectangle
			}
		// se multifirma, definisco la posizione del sigillo al centro del documento
		} else {
			$llx = $lato_firme == 0 ? 100 : 350;

			if($id_modulo == 269) {
				if($num_firma_da_apporre == 1) {
					$lly = 600;
				} elseif($num_firma_da_apporre == 2) {
					$lly = 570;
				} elseif($num_firma_da_apporre == 3) {
					$lly = 350;
				} elseif($num_firma_da_apporre == 4) {
					$lly = 250;
				} elseif($num_firma_da_apporre == 5) {
					$lly = 150;
				}
			} else {
				$lly = 450 - (30 * $num_firma_da_apporre);
			}
			
			$urx = $lato_firme == 0 ? 230 : 480;
			$ury = $lly - 30;
		}
		
		
   		$payloadName = [
		    'applicationId' => 'desktop-signer',
		    'pin' => $pin,
		    'padesSignatures' => array(
		        array(
		            'requestId' => 'id-1',
		            'document' => array(
		                'content' => $file,
		                'contentType' => 'application/pdf',
		            ),
		            'packaging' => 'ENVELOPING',
		            'signatureLevel' => 'BASELINE-B',
					'isVisible' => 'true',
					'signatureFields' => array(
						array(
							'position' => array(
								'page' => 1,
								'llx' => $llx,
								'lly' => $lly,
								'urx' => $urx,
								'ury' => $ury
							),
						),
					),
		        ),
		    ),
		]; 
		
 
		$ch = curl_init($host . "certificates/".$x_signer_id."/sign");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Content-Type: application/json",
			"Otp: $otp",
			"Transaction-Id: $transaction_id",
		    "Authorization: Bearer $token"
		));

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadName, JSON_UNESCAPED_SLASHES));
		//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloadName));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		$return = curl_exec($ch);

		if ($return === FALSE) {
		    return("205 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
		}
//echo "<br><br>HEADER<br>";
//var_dump(curl_getinfo($ch)); 

//echo "<br><br>BODY<br>";
//var_dump(json_encode($payloadName, JSON_UNESCAPED_SLASHES)); 

		curl_close($ch);
		
		return json_decode($return);
	}
	
	function sign_cades_doc($token, $x_signer_id, $pin, $otp, $transaction_id, $file) {
	    if (ConfigInfocert::$mode_stage) {
	        $host = ConfigInfocert::$url_api_stage;
	    } else {
	        $host = ConfigInfocert::$url_api;
	    }
	    
	    $payloadName = [
	        'applicationId' => 'desktop-signer',
	        'pin' => $pin,
	        'cadesSignatures' => array(
	            array(
	                'requestId' => "id-1",
	                'document' => array(
	                    'content' => $file,
	                    'contentType' => "application/pdf",
	                ),
	                'packaging' => "ENVELOPED",
	                'signatureLevel' => "BASELINE-B",
	            ),
	        ),
	    ];
	    
	    $ch = curl_init($host . "certificates/".$x_signer_id."/sign");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        'Content-Type: application/json',
	        "Authorization: Bearer " . $token,
	        "Otp: " . $otp,
	        "Transaction-Id: " . $transaction_id
	    ));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadName, JSON_UNESCAPED_SLASHES));
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $return = curl_exec($ch);
	    
	    if ($return === FALSE) {
	        return("257 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
	    }
	    
	    curl_close($ch);
	    
	    return json_decode($return);
	}