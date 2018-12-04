<?php

namespace Outstack\Enveloper\Infrastructure\History\EmailDeliveryLog\DoctrineOrm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Outstack\Enveloper\Domain\Email\Participants\EmailAddress;
use Outstack\Enveloper\Domain\Email\Participants\Participant;
use Outstack\Enveloper\Domain\Email\Participants\ParticipantList;

class ParticipantListType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return json_encode(array_map([ParticipantType::class, 'fromObject'], iterator_to_array($value->getIterator())));
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return new ParticipantList(array_map([ParticipantType::class, 'toObject'], json_decode($value, true)));
    }


    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    public function getName()
    {
        return 'participant_list';
    }
}