<?php

namespace App\Enums;

enum ModerationStatus: string
{
    case Accepted = 'accepted';
    case MinorChanges = 'minor_changes';
    case MajorChanges = 'major_changes';
    case Rejected = 'rejected';
}
