<?php

namespace Laelaps\Bundle\FacebookAuthentication\Exception;

use Exception;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class InvalidUser extends AuthenticationException
{
    /**
     * @param mixed $item
     * @return string
     */
    private function convertToString($item)
    {
        if (is_string($item)) {
            return $item;
        }

        if (is_array($item)) {
            return sprintf('[array size:%d keys:%s]', count($item), implode(',', array_keys($item)));
        }

        if (is_object($item)) {
            if (method_exists($item, '__toString')) {
                return sprintf('%s:%s', get_class($item), $item);
            }

            return sprintf('%s', get_class($item));
        }

        return strval($item);
    }

    /**
     * @param mixed $user
     * @param \Exception $previous
     */
    public function __construct($user, Exception $previous = null)
    {
        $userString = $this->convertToString($user);

        if ($user instanceof UserInterface) {
            $message = sprintf('"%s" is an instance of UserInterface but is somewhat invalid.', $userString);
        } else {
            $message = sprintf('"%s" (%s) is not a valid user. Expected instance of UserInterface.', $userString, gettype($user));
        }

        return parent::__construct($message, $code = 0, $previous);
    }
}
