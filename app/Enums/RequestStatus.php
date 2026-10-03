<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RequestStatus: string implements HasColor, HasLabel
{
    case Submitted = 'submitted';
    case Reviewing = 'reviewing';
    case Approved = 'approved';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Submitted => 'Diajukan',
            self::Reviewing => 'Ditinjau',
            self::Approved => 'Disetujui',
            self::InProgress => 'Dikerjakan',
            self::Done => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Submitted => 'gray',
            self::Reviewing => 'info',
            self::Approved => 'primary',
            self::InProgress => 'warning',
            self::Done => 'success',
            self::Rejected => 'danger',
        };
    }
}
