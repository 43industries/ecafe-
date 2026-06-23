<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\Database;
use App\Services\Contracts\AuthProviderInterface;
use PDO;

/**
 * Stub for future school portal SSO integration.
 * Set SSO_ENABLED=true and configure SSO_ENDPOINT in .env.
 */
class SSOAuthProvider implements AuthProviderInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function authenticate(string $identifier, string $secret): ?array
    {
        if (!filter_var(env('SSO_ENABLED', false), FILTER_VALIDATE_BOOLEAN)) {
            return null;
        }

        $endpoint = env('SSO_ENDPOINT', '');
        if (!$endpoint) {
            return null;
        }

        // Future: exchange token with school portal API
        // $response = $this->callSSOEndpoint($identifier, $secret);
        // $externalId = $response['external_id'] ?? null;
        // return $this->linkOrCreateStudent($externalId, $response);

        return null;
    }

    public function getProviderName(): string
    {
        return 'sso';
    }

    /**
     * Link external SSO ID to local student record (for future use).
     */
    public function linkStudent(string $externalId, int $studentId, string $accessToken): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO sso_tokens (external_id, provider, access_token, student_id, expires_at)
             VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
             ON DUPLICATE KEY UPDATE access_token = VALUES(access_token), student_id = VALUES(student_id)'
        );
        $stmt->execute([$externalId, 'school_portal', $accessToken, $studentId]);
    }
}
