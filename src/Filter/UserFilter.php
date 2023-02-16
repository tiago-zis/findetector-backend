<?php

namespace App\Filter;

use App\Annotation\UserAware;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;

class UserFilter extends SQLFilter
{

    protected $reader;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $userId = 0;

        $userAware = $targetEntity->getReflectionClass()->getAttributes(UserAware::class)[0] ?? null;
        $fieldName = $userAware?->getArguments()['userFieldName'] ?? null;

        if (!$fieldName) {
            return '';
        }

        try {
            $userId = str_replace("'", "", $this->getParameter('id'));
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        return sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $userId);

    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}