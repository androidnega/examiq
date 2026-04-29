<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionFileType;
use App\Models\ExamSubmission;
use App\Models\SubmissionFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmissionDocumentStorage
{
    public const MAX_BYTES = 1_048_576; // 1 MiB

    /**
     * Store a PDF for this submission version; returns the created SubmissionFile model.
     */
    public function store(
        ExamSubmission $submission,
        int $version,
        SubmissionFileType $type,
        UploadedFile $file,
    ): SubmissionFile {
        $disk = SubmissionFile::STORAGE_DISK;
        $unique = Str::uuid()->toString().'.pdf';
        $directory = sprintf('submissions/%s/v%d', $submission->getKey(), $version);
        $path = $directory.'/'.$unique;

        Storage::disk($disk)->putFileAs($directory, $file, $unique);

        return SubmissionFile::query()->create([
            'submission_id' => $submission->getKey(),
            'version' => $version,
            'type' => $type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}
