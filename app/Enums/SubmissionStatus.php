<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case UnderRevision = 'under_revision';
    case AwaitingHodApproval = 'awaiting_hod_approval';
    case Moderated = 'moderated';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
