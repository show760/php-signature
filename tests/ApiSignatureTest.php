<?php

namespace Token\Tests;

use PHPUnit\Framework\TestCase;
use Token\ApiSignature;
use Token\InvalidKeyOrSecretException;
use Token\InvalidRequestTimestamp;

class ApiSignatureTest extends TestCase
{
    private $key = 'test-key';
    private $secret = 'test-secret';
    private $payload = '{"key1":"value","key2":true,"key3":false,"key4":12.3, "key5":"https://test.com"}';

    /**
     * @dataProvider constructForInvalidKeyOrSecretDataProvider
     *
     * @param string $key
     * @param string $secret
     */
    public function testConstructForInvalidKeyOrSecret(string $key, string $secret)
    {
        $this->expectException(InvalidKeyOrSecretException::class);
        $this->expectExceptionMessage('The key or the secret should not be empty');
        new ApiSignature($key, $secret);
    }

    public function constructForInvalidKeyOrSecretDataProvider()
    {
        return [
            'key is empty string' => ['', $this->secret],
            'secret is empty string' => [$this->key, ''],
        ];
    }

    public function testInvalidRequestUnixTimestamp()
    {
        $timestamp = time() - (15 * 60 + 1);
        $this->expectException(InvalidRequestTimestamp::class);
        $this->expectExceptionMessage('The request unix timestamp is invalid');
        $apiSignature = new ApiSignature($this->key, $this->secret);
        $apiSignature->generateSignature($this->payload, $timestamp);
    }

    public function testValidateTrue()
    {
        $apiSignature = new ApiSignature($this->key, $this->secret);
        $timestamp = time();
        $signature = $apiSignature->generateSignature($this->payload, $timestamp);
        $this->assertTrue($apiSignature->validate($this->payload, $timestamp, $signature));
    }

    public function testValidateFalse()
    {
        $apiSignature = new ApiSignature($this->key, $this->secret);
        $this->assertFalse($apiSignature->validate($this->payload, time(), 'wrong-sign'));
    }
}
