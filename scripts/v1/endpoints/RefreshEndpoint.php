<?php

class RefreshEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (!isset($data->{"user-id"}) || !isset($data->{"client-id"}) || !isset($data->{"refresh-token"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $profile = Backend::fetch_user_profile($data->{"user-id"});
        $clientid = Token::decode($data->{"client-id"});
        $refresh = Token::decode($data->{"refresh-token"});

        if (!Backend::validate_token($clientid, $profile->getUserId(), $refresh)) {
            throw new InvalidTokenException("Invalid refresh token or userid provided");
        }

        Backend::clear_tokens($clientid, $profile->getUserId(), TOKEN_ACCESS);
        $access = Backend::create_token($clientid, $profile->getUserId(), TOKEN_ACCESS, "1 HOUR");

        return array(
            "user-profile" => $profile->toExternalForm(),
            "access-token" => array("token" => $access->toString(), "expires" => 3600)
        );
    }
}