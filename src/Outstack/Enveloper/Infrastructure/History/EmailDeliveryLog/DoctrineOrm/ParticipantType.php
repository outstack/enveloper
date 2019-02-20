<?php

namespace Outstack\Enveloper\Infrastructure\History\EmailDeliveryLog\DoctrineOrm;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Outstack\Enveloper\Domain\Email\Participants\EmailAddress;
use Outstack\Enveloper\Domain\Email\Participants\Participant;

class ParticipantType extends Type
{
    public static function toObject(array $data): Participant
    {
        return new Participant($data['name'], new EmailAddress($data['email']));
    }

    public static function fromObject(Participant $value): array
    {
        if ($value === null) {
            return null;
        }

        return ['name' => $value->getName(), 'email' => (string)$value->getEmailAddress()];
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode(self::fromObject($value));
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);
        return self::toObject($data);
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
        return 'participant';
    }
}