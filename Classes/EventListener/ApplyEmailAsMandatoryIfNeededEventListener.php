<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\EventListener;

use JWeiland\Pforum\Event\PreProcessControllerActionEvent;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

/**
 * Add validator for email in post records, if it is was configured in typoscript
 */
class ApplyEmailAsMandatoryIfNeededEventListener extends AbstractControllerEventListener
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected $allowedControllerActions = [
        'Post' => [
            'create',
            'update'
        ]
    ];

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function __invoke(PreProcessControllerActionEvent $controllerActionEvent): void
    {
        if (
            $this->isValidRequest($controllerActionEvent)
            && ($controllerActionEvent->getSettings()['emailIsMandatory'] ?? false)
            && ($validatorResolver = $this->objectManager->get(ValidatorResolver::class))
            && ($notEmptyValidator = $validatorResolver->createValidator(NotEmptyValidator::class))
            && $notEmptyValidator instanceof NotEmptyValidator
        ) {
            $newPost = $controllerActionEvent->getRequest()->getArgument('newPost');
            $propertyName = 'frontendUser.email';
            if (array_key_exists('anonymousUser', $newPost)) {
                $propertyName = 'anonymousUser.email';
            }

            /** @var ConjunctionValidator $eventValidator */
            $eventValidator = $controllerActionEvent->getArguments()->getArgument('newPost')->getValidator();
            /** @var ConjunctionValidator $conjunctionValidator */
            $conjunctionValidator = $eventValidator->getValidators()->current();
            /** @var GenericObjectValidator $genericEventValidator */
            $genericEventValidator = $conjunctionValidator->getValidators()->current();
            $genericEventValidator->addPropertyValidator(
                $propertyName,
                $notEmptyValidator
            );
        }
    }
}
