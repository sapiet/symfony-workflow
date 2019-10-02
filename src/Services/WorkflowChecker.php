<?php

namespace App\Services;

use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;

/**
 * Class WorkflowChecker
 * @package App\Services
 *
 * This WorkflowChecker contains the following features
 *   - Check if new value is available in workflows places
 *   - Retrieve a matching transition and check if it can be applied
 */
class WorkflowChecker
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function check($entity, array $changeSet)
    {
        foreach ($changeSet as $field => $values) {
            $setter = 'set'.ucfirst($field);
            $cloned = (clone $entity)->$setter($values[0]);

            $this->ensureValueIsAvailable($entity, $field, $values[1]);
            $this->ensureCan($cloned, $field, $values[0], $values[1]);
        }
    }

    private function ensureValueIsAvailable($entity, $field, $value): void
    {
        foreach ($this->registry->all($entity) as $stateMachine) {
            $property = $stateMachine->getMarkingStore()->property;

            if ($field === $property) {
                if (!in_array($value, $stateMachine->getDefinition()->getPlaces())) {
                    $error = sprintf(
                        'Value "%s" is not available for "%s" property in %s workflow',
                        $value,
                        $field,
                        $stateMachine->getName()
                    );

                    throw new LogicException($error);
                }
            }
        }
    }

    private function ensureCan($entity, string $field, $beforeValue, $afterValue): void
    {
        $result = $this->getMatchingTransition($entity, $field, $beforeValue, $afterValue);

        if (false === $result) {
            $error = sprintf(
                'Unable to find any transition to update %s property from %s to %s',
                $field,
                $beforeValue,
                $afterValue
            );
            throw new LogicException($error);
        }

        if (null !== $result) {
            list($stateMachine, $transitionName) = $result;

            if (false === $stateMachine->can($entity, $transitionName)) {
                $error = sprintf(
                    'Cannot update "%s" property from "%s" to "%s" because of "%s" workflow (transition: %s)',
                    $field,
                    $beforeValue,
                    $afterValue,
                    $stateMachine->getName(),
                    $transitionName
                );

                throw new LogicException($error);
            }
        }
    }

    public function getStateMachine($entity, string $field): ?StateMachine
    {
        foreach ($this->registry->all($entity) as $stateMachine) {
            $property = $stateMachine->getMarkingStore()->property;

            if ($field === $property) {
                return $stateMachine;
            }
        }

        return null;
    }

    private function getMatchingTransition($entity, string $field, $beforeValue, $afterValue)
    {
        foreach ($this->registry->all($entity) as $stateMachine) {
            $property = $stateMachine->getMarkingStore()->property;

            if ($field === $property) {
                foreach ($stateMachine->getDefinition()->getTransitions() as $transition) {
                    if (in_array($beforeValue, $transition->getFroms()) && in_array($afterValue, $transition->getTos())) {
                        return [$stateMachine, $transition->getName()];
                    }
                }

                // No transition found
                return false;
            }
        }

        return null;
    }
}
