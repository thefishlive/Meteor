<?php

class HandshakeEndpoint extends Endpoint {

	public function handle($body) {
		$data = json_decode($body);
		
		if (!isset($data->{"user-id"}) || !isset($data->{"client-id"})) {
			throw new EndpointExecutionException("Invalid request");
		}
		
		$userid = Token::decode($data->{"user-id"});
		$token = Token::generateToken(TOKEN_REQUEST, $userid->getUserSecret());
		
		$query = "INSERT INTO " . DATABASE_TABLE_TOKENS . " (`token`, `user`, `expires`) VALUES ('" . $token->toString() . "','" . $userid->toString() . "', NOW() + INTERVAL 1 HOUR);";
		
		$result = Database::query($query);
		
		if (!$result) {
			throw new EndpointExecutionException("An error occurred adding token to database", array("query" => $query));
		}
		
		return array("client-id" => $data->{"client-id"},
					"user-id" => $userid->toString(),
					"refresh" => $token->toString(),
					"expires" => 3600);
	}
}