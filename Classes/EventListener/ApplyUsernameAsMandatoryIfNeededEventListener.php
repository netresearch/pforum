<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\EventListener;

use JWeiland\Pforum\Event\PreProcessControllerActionEvent;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

/**
 * Add validator for username in topic/post records, if it is was configured in typoscript.
 */
class ApplyUsernameAsMandatoryIfNeededEventListener extends AbstractControllerEventListener
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected $allowedControllerActions = [
        'Topic' => [
            'create',
            'update',
        ],
        'Post' => [
            'create',
            'update',
        ],
    ];

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function __invoke(PreProcessControllerActionEvent $controllerActionEvent): void
    {
        if (
            $this->isValidRequest($controllerActionEvent)
            && ($controllerActionEvent->getSettings()['usernameIsMandatory'] ?? false)
            && ($validatorResolver = $this->objectManager->get(ValidatorResolver::class))
            && ($notEmptyValidator = $validatorResolver->createValidator(NotEmptyValidator::class))
            && $notEmptyValidator instanceof NotEmptyValidator
            && ($argumentName = $this->getArgumentName($controllerActionEvent))
        ) {
            /** @var ConjunctionValidator $eventValidator */
            $eventValidator = $controllerActionEvent
                ->getArguments()
                ->getArgument($argumentName)
                ->getValidator();

            /** @var ConjunctionValidator $conjunctionValidator */
            $conjunctionValidator = $eventValidator
                ->getValidators()
                ->current();

            /** @var GenericObjectValidator $genericEventValidator */
            $genericEventValidator = $conjunctionValidator
                ->getValidators()
                ->current();

            $propertyName = $this->getUsersPropertyName(
                $controllerActionEvent->getRequest(),
                $argumentName
            );

            if ($propertyName !== '') {
                $genericEventValidator->addPropertyValidator(
                    $propertyName,
                    $notEmptyValidator
                );
            }
        }
    }

    protected function getUsersPropertyName(Request $request, string $argumentName): string
    {
        $requestedArgument = $this->getRequestedArgument($request, $argumentName);
        if ($requestedArgument === []) {
            return '';
        }

        if (array_key_exists('anonymousUser', $requestedArgument)) {
            return 'anonymousUser.username';
        }

        if (array_key_exists('frontendUser', $requestedArgument)) {
            return 'frontendUser.username';
        }

        return '';
    }

    protected function getRequestedArgument(Request $request, string $argumentName): array
    {
        if ($argumentName === '') {
            return [];
        }

        if ($request->hasArgument($argumentName)) {
            return $request->getArgument($argumentName);
        }

        return [];
    }

    protected function getArgumentName(PreProcessControllerActionEvent $event): string
    {
        if ($event->getControllerName() === 'Topic') {
            return 'topic';
        }

        if ($event->getControllerName() === 'Post') {
            return 'post';
        }

        return '';
    }
}
