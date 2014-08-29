<?php namespace Oss2\Auth\Extensions\Handlers;

/**
 * Oss2/Auth
 *
 * Handler fo the MaxFailed extension
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
abstract class Handler
{
    /** @var array The configuration for the extension */
    protected $config = null;

    public function __construct()
    {
        $this->config = \Auth::getExtension( $this->getExtensionName() )->getConfig();
    }

    /**
     * Get the name of the extension
     *
     * Must be overridden in the subclass
     *
     * @return string Extension name
     */
    public function getExtensionName()
    {
        throw new \Oss2\Auth\Extensions\Exception( 'You must override this function' );
    }
}
