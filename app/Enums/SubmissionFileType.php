<?php

namespace App\Enums;

enum SubmissionFileType: string
{
    case Questions = 'questions';
    case MarkingScheme = 'marking_scheme';
    case Outline = 'outline';
    case Supporting = 'supporting';

    public function label(): string
    {
        return match ($this) {
            self::Questions => __('Exam questions'),
            self::MarkingScheme => __('Marking scheme'),
            self::Outline => __('Course outline'),
            self::Supporting => __('Supporting document'),
        };
    }
}
