<?php namespace Oss2\Auth\Extensions\Interfaces;

/**
 * Oss2/Auth
 *
 * Interface for the TwoFactor extension
 *
 * User classes must implement these functions for this extension.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.md.
 *
 * @category   Authentication
 * @package    Oss2\Auth
 * @copyright  Copyright (c) 2014, Open Source Solutions Limited, Dublin, Ireland
 */
interface TwoFactorUserInterface
{
    /**
     * Return the current counter for sequential failed authentication attempts
     * @return bool
     */
    public function auth2faEnabled();

}
