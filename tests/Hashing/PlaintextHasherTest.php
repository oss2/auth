<?php

/**
 * Oss2/Auth
 *
 * Test the plaintext hasher
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.md.
 *
 * @category   Authentication
 * @package    Oss2\Auth
 * @copyright  Copyright (c) 2014, Open Source Solutions Limited, Dublin, Ireland
 */
class PlaintextHasherTest extends PHPUnit_Framework_TestCase {

    public function testBasicHashing()
    {
        $hasher = new Oss2\Auth\Hashing\PlaintextHasher;
        $value = $hasher->make('password');
        $this->assertNotSame('otherpassword', $value);
        $this->assertTrue($hasher->check('password', $value));
        $this->assertFalse($hasher->needsRehash($value));
    }
}
