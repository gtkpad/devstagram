<?php

namespace Service;

class Jwt {


	public static function create($data) {
        global $config;

		$header = json_encode(array("typ"=>"JWT", "alg"=>"HS256"));

		$payload = json_encode($data);

		$hbase = self::base64url_encode($header);
		$pbase = self::base64url_encode($payload);

		$signature = hash_hmac("sha256", $hbase.".".$pbase, $config['jwt_secret_key'], true);
		$bsig = self::base64url_encode($signature);

		$jwt = $hbase.".".$pbase.".".$bsig;

		return $jwt;
	}

	public static function validate($token) {
        global $config;
		// Passo 1: Verificar se o TOKEN tem 3 partes.
		// Passo 2: Bater a assinatura com os dados
		$array = array();

		$jwt_split = explode('.', $token);

		if(count($jwt_split) == 3) {
			$signature = hash_hmac("sha256", $jwt_split[0].".".$jwt_split[1], $config['jwt_secret_key'], true);
			$bsig = self::base64url_encode($signature);

			if($bsig == $jwt_split[2]) {

				$array = json_decode(self::base64url_decode($jwt_split[1]));
				return $array;

			} else {
				return false;
			}

		} else {
			return false;
		}

	}

	private static function base64url_encode( $data ){
	  return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
	}

	private static function base64url_decode( $data ){
	  return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
	}

}