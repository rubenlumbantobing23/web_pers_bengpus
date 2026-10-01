<?php

namespace App\Services;

use App\Models\MarriageApplication;
use App\Models\MarriageDocumentType;
use App\Models\MarriageLetter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MarriageApplicationLetterService
{
    public const INITIAL_CODE = 'SURAT_PERMOHONAN_IZIN_NIKAH';
    public const COVER_CODES = [
        'PENGANTAR_NA',
        'PENGANTAR_KESDAM',
        'PENGANTAR_BINTALDAM',
        'PENGANTAR_LITPERS',
        'PENGANTAR_SKBD',
        'SURAT_KETERANGAN_BELUM_NIKAH',
    ];
    public const FINAL_CODE = 'SURAT_IZIN_NIKAH_FINAL';

    public function __construct(private MarriageLetterGenerator $generator)
    {
    }

    /** Generate eligible letters once, retrying missing templates/files on later requests. */
    public function ensureGenerated(MarriageApplication $application, array $codes, int $generatedBy, array $forceCodes = []): void
    {
        foreach (array_unique($codes) as $code) {
            try {
                $type = MarriageDocumentType::where('code', $code)->where('is_active', true)->first();
                if (!$type || !$type->template_path) {
                    continue;
                }

                $templatePath = storage_path('app/' . $type->template_path);
                if (!is_file($templatePath)) {
                    Log::warning('Marriage letter template is unavailable.', [
                        'application_id' => $application->id,
                        'letter_code' => $code,
                        'template_path' => $type->template_path,
                    ]);
                    continue;
                }

                $existing = MarriageLetter::where('marriage_application_id', $application->id)
                    ->where('jenis_surat', $code)
                    ->first();
                $force = in_array($code, $forceCodes, true);

                if (!$force && $existing && Storage::disk('private')->exists($existing->file_generated)) {
                    $templateWasUpdated = !$existing->generated_at
                        || filemtime($templatePath) > $existing->generated_at->getTimestamp();
                    if (!$templateWasUpdated) {
                        continue;
                    }
                }

                $personelName = $application->personel?->nama ?? 'personel';
                $outputName = $code . '_' . $application->id . '_' . $personelName;
                $newPath = $this->generator->generate($application, $templatePath, $outputName);

                MarriageLetter::updateOrCreate(
                    [
                        'marriage_application_id' => $application->id,
                        'jenis_surat' => $code,
                    ],
                    [
                        'file_generated' => $newPath,
                        'status' => 'TERSEDIA',
                        'generated_at' => now(),
                        'generated_by' => $generatedBy,
                    ]
                );

                if ($existing && $existing->file_generated !== $newPath) {
                    Storage::disk('private')->delete($existing->file_generated);
                }
            } catch (\Throwable $exception) {
                Log::warning('Automatic marriage letter generation failed.', [
                    'application_id' => $application->id,
                    'letter_code' => $code,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
