<?php
namespace meteor\data;
use InvalidArgumentException;

/**
 * Represents a user's profile
 */
class UserProfile
{
    /**
     * @var Token
     */
    private $userid;

    /**
     * @var String
     */
    private $displayName;

    /**
     * Instantiate a new Profile
     *
     * @param null|Token $userid
     * @param null|String $displayName
     */
    public function __construct(Token $userid = null, $displayName = null)
    {
        if ($userid == null && $displayName == null) {
            throw new InvalidArgumentException("Both id and display name cannot be null");
        }

        $this->userid = $userid;
        $this->displayName = $displayName;
    }

    /**
     * Gets the user's user id
     *
     * @return Token the user's user id
     */
    public function getUserId()
    {
        return $this->userid;
    }

    /**
     * Gets the user's display name
     *
     * @return String the user's display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Checks if this profile represents the same user as another.
     *
     * @param UserProfile $profile the profile to check
     *
     * @return bool true if they are for the same user, false otherwise
     */
    public function equals(UserProfile $profile)
    {
        return $this->getUserId() == $profile->getUserId();
    }

    /**
     * Convert this user profile into a json encodedable form to return from the API
     *
     * @return array this profile as an array
     */
    public function toExternalForm()
    {
        return ["user-id" => $this->userid->toString(), "display-name" => $this->displayName];
    }

} 