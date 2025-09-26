<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DocumentNumberingService
{
    /**
     * Document type codes (alphabetical order)
     */
    const DOCUMENT_CODES = [
        'asset_disposals' => '01',
        'cash_expenses' => '02',
        'cash_ins' => '03',
        'cash_outs' => '04',
        'goods_receipts' => '05',
        'journals' => '06',
        'purchase_invoices' => '07',
        'purchase_orders' => '08',
        'purchase_payments' => '09',
        'sales_invoices' => '10',
        'sales_orders' => '11',
        'sales_receipts' => '12',
    ];

    /**
     * Generate document number for given document type
     * Format: YY + DOCUMENT_CODE + SEQUENCE (10 digits total)
     * 
     * @param string $documentType
     * @param string|null $date Optional date for year calculation
     * @return string
     */
    public function generateNumber(string $documentType, string $date = null): string
    {
        if (!isset(self::DOCUMENT_CODES[$documentType])) {
            throw new \InvalidArgumentException("Unknown document type: {$documentType}");
        }

        $year = $date ? date('y', strtotime($date)) : date('y');
        $documentCode = self::DOCUMENT_CODES[$documentType];

        // Get next sequence number for this document type and year
        $sequence = $this->getNextSequence($documentType, $year);

        return sprintf('%s%s%06d', $year, $documentCode, $sequence);
    }

    /**
     * Get next sequence number for document type and year
     * Uses database table to track sequences with yearly reset
     * 
     * @param string $documentType
     * @param string $year
     * @return int
     */
    private function getNextSequence(string $documentType, string $year): int
    {
        $cacheKey = "doc_sequence_{$documentType}_{$year}";

        return Cache::lock("sequence_lock_{$documentType}_{$year}", 10)->get(function () use ($documentType, $year, $cacheKey) {
            // Check if we have cached sequence
            $cachedSequence = Cache::get($cacheKey);
            if ($cachedSequence !== null) {
                $nextSequence = $cachedSequence + 1;
                Cache::put($cacheKey, $nextSequence, 3600); // Cache for 1 hour
                return $nextSequence;
            }

            // Get current sequence from database or start from 1
            $currentSequence = DB::table('document_sequences')
                ->where('document_type', $documentType)
                ->where('year', $year)
                ->value('current_sequence') ?? 0;

            $nextSequence = $currentSequence + 1;

            // Update or insert sequence record
            DB::table('document_sequences')->updateOrInsert(
                [
                    'document_type' => $documentType,
                    'year' => $year
                ],
                [
                    'current_sequence' => $nextSequence,
                    'updated_at' => now()
                ]
            );

            // Cache the sequence
            Cache::put($cacheKey, $nextSequence, 3600);

            return $nextSequence;
        });
    }

    /**
     * Get document type from document number
     * 
     * @param string $documentNumber
     * @return string|null
     */
    public function getDocumentTypeFromNumber(string $documentNumber): ?string
    {
        if (strlen($documentNumber) !== 10) {
            return null;
        }

        $documentCode = substr($documentNumber, 2, 2);

        return array_search($documentCode, self::DOCUMENT_CODES) ?: null;
    }

    /**
     * Get year from document number
     * 
     * @param string $documentNumber
     * @return string|null
     */
    public function getYearFromNumber(string $documentNumber): ?string
    {
        if (strlen($documentNumber) !== 10) {
            return null;
        }

        return substr($documentNumber, 0, 2);
    }

    /**
     * Get sequence from document number
     * 
     * @param string $documentNumber
     * @return int|null
     */
    public function getSequenceFromNumber(string $documentNumber): ?int
    {
        if (strlen($documentNumber) !== 10) {
            return null;
        }

        return (int) substr($documentNumber, 4, 6);
    }

    /**
     * Validate document number format
     * 
     * @param string $documentNumber
     * @return bool
     */
    public function isValidFormat(string $documentNumber): bool
    {
        if (strlen($documentNumber) !== 10 || !ctype_digit($documentNumber)) {
            return false;
        }

        $documentCode = substr($documentNumber, 2, 2);
        return in_array($documentCode, self::DOCUMENT_CODES);
    }

    /**
     * Get all document types and their codes
     * 
     * @return array
     */
    public function getDocumentTypes(): array
    {
        return self::DOCUMENT_CODES;
    }
}
