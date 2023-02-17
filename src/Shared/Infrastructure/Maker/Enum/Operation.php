<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Enum;

enum Operation: string
{
    case BROWSE = 'browse';
    case READ = 'read';
    case EDIT = 'edit';
    case ADD = 'add';
    case DELETE = 'delete';

    public static function getValues()
    {
        return [
            self::BROWSE->value,
            self::READ->value,
            self::EDIT->value,
            self::ADD->value,
            self::DELETE->value,
        ];
    }

    public function entryClass(): string
    {
        return match ($this) {
            self::BROWSE => 'Browser',
            self::READ => 'Reader',
            self::EDIT => 'Updater',
            self::ADD => 'Creator',
            self::DELETE => 'Deleter',
        };
    }

    public function entryMethod(): string
    {
        return match ($this) {
            self::BROWSE => 'browse',
            self::READ => 'read',
            self::EDIT => 'update',
            self::ADD => 'add',
            self::DELETE => 'delete',
        };
    }

    public function type(): string
    {
        return match ($this) {
            self::BROWSE, self::READ => 'Provider',
            self::EDIT, self::ADD, self::DELETE => 'Persister',
        };
    }

    public function exception(): string
    {
        return match ($this) {
            self::ADD => 'UnableToCreate',
            self::READ => 'Unknown',
            self::EDIT => 'UnableToUpdate',
            self::DELETE => 'UnableToDelete',
        };
    }

    public function event(): string
    {
        return match ($this) {
            self::ADD => 'WasCreated',
            self::EDIT => 'WasUpdated',
            self::DELETE => 'WasDeleted',
        };
    }

    public function operationType(): string
    {
        return match ($this) {
            self::BROWSE, self::READ => 'Read',
            self::EDIT, self::ADD, self::DELETE => 'Write',
        };
    }

    public function operationDTO(): string
    {
        return match ($this) {
            self::BROWSE, self::READ => 'Query',
            self::EDIT, self::ADD, self::DELETE => 'Command',
        };
    }
}
