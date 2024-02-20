<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Tests\Functional\Validation\Validator;

use Doctrine\DBAL\DBALException;
use JWeiland\Pforum\Validation\Validator\EmailValidator;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case
 */
class EmailValidatorTest extends FunctionalTestCase
{
    use ProphecyTrait;

    /**
     * @var EmailValidator
     */
    protected EmailValidator $subject;

    /**
     * @var ConfigurationManagerInterface|ObjectProphecy
     */
    protected ObjectProphecy|ConfigurationManagerInterface $configurationManagerProphecy;

    /**
     * @var array
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/pforum'
    ];

    /**
     * @return void
     * @throws DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $this->configurationManagerProphecy = $this->prophesize(ConfigurationManager::class);

        $this->subject = new EmailValidator();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function validateWillNotAddAnyErrorIfEmailIsNotMandatory(): void
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
    public function validateWillNotAddAnyErrorIfEmailIsNotString(): void
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
    public function validateWillNotAddAnyErrorIfEmailIsValidAndIsString(): void
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
    public function validateWillAddErrorIfEmailIsStringAndEmpty(): void
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
    public function validateWillAddErrorIfEmailIsStringAndNotValid(): void
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

    /**
     * @param bool $isMandatory
     *
     * @return void
     * @throws InvalidConfigurationTypeException
     */
    protected function setEmailIsMandatory(bool $isMandatory): void
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
