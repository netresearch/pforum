<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Tests\Functional\Validation\Validator;

use JWeiland\Pforum\Validation\Validator\UsernameValidator;
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
class UsernameValidatorTest extends FunctionalTestCase
{
    /**
     * @var UsernameValidator
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

        $this->subject = new UsernameValidator();
    }

    public function tearDown()
    {
        unset($this->subject);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfUsernameIsNotMandatory()
    {
        $this->setUsernameIsMandatory(false);

        self::assertEquals(
            new Result(),
            $this->subject->validate('')
        );
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfUsernameIsNotString()
    {
        $this->setUsernameIsMandatory(true);

        self::assertEquals(
            new Result(),
            $this->subject->validate(123)
        );
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfUsernameIsNotEmpty()
    {
        $this->setUsernameIsMandatory(true);

        self::assertEquals(
            new Result(),
            $this->subject->validate('stefan')
        );
    }

    /**
     * @test
     */
    public function validateWillAddErrorIfUsernameIsEmpty()
    {
        $this->setUsernameIsMandatory(true);

        $expectedResult = new Result();
        $expectedResult->addError(
            new Error(
                'The username of user object is mandatory',
                1378304890
            )
        );

        self::assertEquals(
            $expectedResult,
            $this->subject->validate('')
        );
    }

    protected function setUsernameIsMandatory(bool $isMandatory)
    {
        $this->configurationManagerProphecy
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'pforum',
                'forum'
            )
            ->shouldBeCalled()
            ->willReturn([
                'usernameIsMandatory' => $isMandatory ? '1' : '0'
            ]);
        $this->subject->injectConfigurationManager($this->configurationManagerProphecy->reveal());
    }
}
