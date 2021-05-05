<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Tests\Functional\Configuration;

use JWeiland\Pforum\Configuration\ExtConf;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

/**
 * Test case
 */
class ExtConfTest extends FunctionalTestCase
{
    /**
     * @var ExtConf
     */
    protected $subject;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/pforum'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->subject = new ExtConf();
    }

    public function tearDown()
    {
        unset($this->subject);
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = '';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = '';
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getEmailFromAddressInitiallyThrowsException()
    {
        self::expectExceptionCode(1604694223);
        $this->subject->getEmailFromAddress();
    }

    /**
     * @test
     */
    public function getEmailFromAddressInitiallyReturnsEmailFromInstallTool()
    {
        $email = 'info@example.com';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = $email;
        self::assertSame(
            $email,
            $this->subject->getEmailFromAddress()
        );
    }

    /**
     * @test
     */
    public function setEmailFromAddressSetsEmailAddress()
    {
        $email = 'abc@example.com';
        $this->subject->setEmailFromAddress($email);

        self::assertSame(
            $email,
            $this->subject->getEmailFromAddress()
        );
    }

    /**
     * @test
     */
    public function getEmailFromNameInitiallyThrowsException()
    {
        self::expectExceptionCode(1604694279);
        $this->subject->getEmailFromName();
    }

    /**
     * @test
     */
    public function getEmailFromNameInitiallyReturnsEmailNameFromInstallTool()
    {
        $name = 'stefan';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = $name;
        self::assertSame(
            $name,
            $this->subject->getEmailFromName()
        );
    }

    /**
     * @test
     */
    public function setEmailFromNameSetsEmailName()
    {
        $name = 'stefan';
        $this->subject->setEmailFromName($name);

        self::assertSame(
            $name,
            $this->subject->getEmailFromName()
        );
    }
}
