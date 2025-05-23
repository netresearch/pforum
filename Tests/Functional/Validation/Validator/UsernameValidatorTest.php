<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Tests\Functional\Validation\Validator;

use JWeiland\Pforum\Validation\Validator\UsernameValidator;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class UsernameValidatorTest extends FunctionalTestCase
{
    use ProphecyTrait;

    /**
     * @var UsernameValidator
     */
    protected UsernameValidator $subject;

    /**
     * @var ConfigurationManagerInterface|ObjectProphecy
     */
    protected ObjectProphecy|ConfigurationManagerInterface $configurationManagerProphecy;

    /**
     * @var array
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/pforum',
    ];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['LANG']                    = GeneralUtility::makeInstance(LanguageService::class);
        $this->configurationManagerProphecy = $this->prophesize(ConfigurationManager::class);

        $this->subject = new UsernameValidator();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->subject);

        parent::tearDown();
    }

    #[Test]
    public function validateWillNotAddAnyErrorIfUsernameIsNotMandatory(): void
    {
        $this->setUsernameIsMandatory(false);

        self::assertEquals(
            new Result(),
            $this->subject->validate('')
        );
    }

    #[Test]
    public function validateWillNotAddAnyErrorIfUsernameIsNotString(): void
    {
        $this->setUsernameIsMandatory(true);

        self::assertEquals(
            new Result(),
            $this->subject->validate(123)
        );
    }

    #[Test]
    public function validateWillNotAddAnyErrorIfUsernameIsNotEmpty(): void
    {
        $this->setUsernameIsMandatory(true);

        self::assertEquals(
            new Result(),
            $this->subject->validate('stefan')
        );
    }

    #[Test]
    public function validateWillAddErrorIfUsernameIsEmpty(): void
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

    /**
     * @param bool $isMandatory
     *
     * @return void
     */
    protected function setUsernameIsMandatory(bool $isMandatory): void
    {
        $this->configurationManagerProphecy
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'pforum',
                'forum'
            )
            ->shouldBeCalled()
            ->willReturn([
                'usernameIsMandatory' => $isMandatory ? '1' : '0',
            ]);

        $this->subject->injectConfigurationManager($this->configurationManagerProphecy->reveal());
    }
}
