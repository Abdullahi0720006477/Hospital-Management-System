<?php

namespace App\Service;

use App\Entity\MedicalRecord;

class ClinicalIntelligenceService
{
    /**
     * Calculates a simplified NEWS2 (National Early Warning Score)
     * Returns a score (0-20) and a risk level.
     */
    public function calculateRiskScore(MedicalRecord $record): array
    {
        $vitals = $record->getVitalSigns() ?? [];
        $score = 0;
        $alerts = [];

        // 1. Blood Pressure (Systolic)
        $bp = explode('/', $vitals['bp'] ?? '120/80')[0];
        if ($bp <= 90 || $bp >= 220) {
            $score += 3;
            $alerts[] = 'Critical Blood Pressure';
        } elseif ($bp <= 100 || ($bp >= 111 && $bp <= 219)) {
            // Normal ranges vary, simplified for demo
        }

        // 2. Temperature
        $temp = (float) ($vitals['temp'] ?? 37.0);
        if ($temp <= 35.0 || $temp >= 39.1) {
            $score += 3;
            $alerts[] = 'Critical Temperature (Febrile/Hypothermic)';
        } elseif (($temp >= 35.1 && $temp <= 36.0) || ($temp >= 38.1 && $temp <= 39.0)) {
            $score += 1;
        }

        // 3. Heart Rate (Simulated if missing)
        $hr = (int) ($vitals['heart_rate'] ?? rand(60, 110));
        if ($hr <= 40 || $hr >= 131) {
            $score += 3;
            $alerts[] = 'Tachycardia/Bradycardia Warning';
        }

        // Risk Level Mapping
        $level = 'Low';
        $color = 'success';
        if ($score >= 7) {
            $level = 'ERERGENCY';
            $color = 'danger';
        } elseif ($score >= 5) {
            $level = 'High';
            $color = 'danger';
        } elseif ($score >= 3) {
            $level = 'Medium';
            $color = 'warning';
        }

        return [
            'score' => $score,
            'level' => $level,
            'color' => $color,
            'alerts' => $alerts,
            'recommendation' => $score >= 5 ? 'Immediate clinician review required' : 'Routine monitoring'
        ];
    }
}
