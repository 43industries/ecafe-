<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StudentModel;

class LoyaltyService
{
    private StudentModel $students;
    private int $pointsPerUnit;

    public function __construct()
    {
        $this->students = new StudentModel();
        $config = require ECAFE_ROOT . '/config/app.php';
        $this->pointsPerUnit = $config['loyalty_points_per_unit'];
    }

    public function calculateEarned(float $amount): int
    {
        return (int) floor($amount * $this->pointsPerUnit);
    }

    public function redeemPoints(int $studentId, int $points): float
    {
        $student = $this->students->findById($studentId);
        if (!$student || $student['loyalty_points'] < $points) {
            return 0;
        }
        $discount = $points / 100; // 100 points = 1 KES
        $this->students->updateLoyaltyPoints($studentId, -$points);
        return $discount;
    }

    public function awardPoints(int $studentId, float $amount): void
    {
        $points = $this->calculateEarned($amount);
        if ($points > 0) {
            $this->students->updateLoyaltyPoints($studentId, $points);
        }
    }
}
