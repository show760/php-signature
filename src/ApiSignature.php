<?php

namespace Token;

class ApiSignature
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * ApiToken constructor.
     * @param string $key $key
     * @param string $secret $secret
     */
    public function __construct(string $key, string $secret)
    {
        if (empty($key) || empty($secret)) {
            throw new InvalidKeyOrSecretException('The key or the secret should not be empty');
        }
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param string $payload JSON string
     * @param int $timestamp Request unix timestamp
     * @return string
     */
    public function generateSignature(string $payload, int $timestamp)
    {
        if ($timestamp < time() - 15 * 60) {
            throw new InvalidRequestTimestamp('The request unix timestamp is invalid');
        }

        $message = $this->key . $payload . $timestamp;
        return base64_encode(hash_hmac('sha256', $message, $this->secret, true));
    }

    /**
     * @param string $payload JSON string
     * @param int $timestamp Request unix timestamp
     * @param string $signature The signature which would be validated
     * @return bool
     */
    public function validate(string $payload, int $timestamp, string $signature)
    {
        $expectSignature = $this->generateSignature($payload, $timestamp);
        return $expectSignature === $signature;
    }
}
