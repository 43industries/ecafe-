<div class="card-ecafe p-4">
    <h5 class="mb-4">System Settings</h5>
    <p class="text-muted">Configure environment variables in the <code>.env</code> file:</p>
    <ul>
        <li><strong>Database:</strong> DB_HOST, DB_NAME, DB_USER, DB_PASS</li>
        <li><strong>M-Pesa:</strong> MPESA_CONSUMER_KEY, MPESA_CONSUMER_SECRET, MPESA_PASSKEY, MPESA_SHORTCODE, MPESA_CALLBACK_URL</li>
        <li><strong>Email:</strong> MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD</li>
        <li><strong>SSO (future):</strong> SSO_ENABLED, SSO_ENDPOINT, SSO_CLIENT_ID</li>
        <li><strong>SMS (optional):</strong> SMS_ENABLED, SMS_API_URL, SMS_API_KEY</li>
    </ul>
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>
        For M-Pesa sandbox testing, use Safaricom Daraja portal credentials and expose your callback URL via ngrok.
    </div>
</div>
