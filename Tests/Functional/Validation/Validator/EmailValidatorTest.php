<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Tests\Functional\Validation\Validator;

use JWeiland\Pforum\Validation\Validator\EmailValidator;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Validation\Error;

/**
 * Test case
 */
class EmailValidatorTest extends FunctionalTestCase
{
    /**
     * @var EmailValidator
     */
    protected $subject;

    /**
     * @var ConfigurationManagerInterface|ObjectProphecy
     */
    protected $configurationManagerProphecy;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/pforum'
    ];

    public function setUp()
    {
        parent::setUp();
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $this->configurationManagerProphecy = $this->prophesize(ConfigurationManager::class);

        $this->subject = new EmailValidator();
    }

    public function tearDown()
    {
        unset($this->subject);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfEmailIsNotMandatory()
    {
        $this->setEmailIsMandatory(false);

        self::assertEquals(
            new Result(),
            $this->subject->validate('hello')
        );
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfEmailIsNotString()
    {
        $this->setEmailIsMandatory(true);

        self::assertEquals(
            new Result(),
            $this->subject->validate(123)
        );
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfEmailIsValidAndIsString()
    {
        $this->setEmailIsMandatory(true);

        self::assertEquals(
            new Result(),
            $this->subject->validate('info@example.com')
        );
    }

    /**
     * @test
     */
    public function validateWillAddErrorIfEmailIsStringAndEmpty()
    {
        $this->setEmailIsMandatory(true);

        $expectedResult = new Result();
        $expectedResult->addError(
            new Error(
                'The email of user object is mandatory',
                1378288238
            )
        );

        self::assertEquals(
            $expectedResult,
            $this->subject->validate('')
        );
    }

    /**
     * @test
     */
    public function validateWillAddErrorIfEmailIsStringAndNotValid()
    {
        $this->setEmailIsMandatory(true);

        $expectedResult = new Result();
        $expectedResult->addError(
            new Error(
                'The email of user object is not a valid email',
                1457431804
            )
        );

        self::assertEquals(
            $expectedResult,
            $this->subject->validate('hello')
        );
    }

    protected function setEmailIsMandatory(bool $isMandatory)
    {
        $this->configurationManagerProphecy
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'pforum',
                'forum'
            )
            ->shouldBeCalled()
            ->willReturn([
                'emailIsMandatory' => $isMandatory ? '1' : '0'
            ]);
        $this->subject->injectConfigurationManager($this->configurationManagerProphecy->reveal());
    }
}
