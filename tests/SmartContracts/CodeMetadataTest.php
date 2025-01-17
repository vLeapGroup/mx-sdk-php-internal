<?php

namespace Tests\SmartContracts;

use Exception;
use PHPUnit\Framework\TestCase;
use MultiversX\SmartContracts\CodeMetadata;

class CodeMetadataTest extends TestCase
{
    public function testShouldCreateDefaultCodeMetadataInstance(): void
    {
        $metadata = new CodeMetadata();
        $this->assertTrue($metadata->upgradeable);
        $this->assertFalse($metadata->readable);
        $this->assertFalse($metadata->payable);
        $this->assertFalse($metadata->payableBySc);
    }

    public function testShouldTogglePropertiesCorrectly(): void
    {
        $metadata = new CodeMetadata();
        $metadata->toggleUpgradeable(false);
        $metadata->toggleReadable(true);
        $metadata->togglePayable(true);
        $metadata->togglePayableBySc(true);

        $this->assertFalse($metadata->upgradeable);
        $this->assertTrue($metadata->readable);
        $this->assertTrue($metadata->payable);
        $this->assertTrue($metadata->payableBySc);
    }

    public function testShouldConvertToBufferCorrectly(): void
    {
        $metadata = new CodeMetadata(true, true, true, true);
        $buffer = $metadata->toBuffer();

        $this->assertEquals(2, strlen($buffer));
        $this->assertEquals(
            CodeMetadata::ByteZeroUpgradeable | CodeMetadata::ByteZeroReadable,
            ord($buffer[0])
        );
        $this->assertEquals(
            CodeMetadata::ByteOnePayable | CodeMetadata::ByteOnePayableBySc,
            ord($buffer[1])
        );
    }

    public function testShouldCreateFromBufferCorrectlyWhenAllFlagsAreSet(): void
    {
        $buffer = chr(CodeMetadata::ByteZeroUpgradeable | CodeMetadata::ByteZeroReadable) .
                 chr(CodeMetadata::ByteOnePayable | CodeMetadata::ByteOnePayableBySc);
        $metadata = CodeMetadata::fromBuffer($buffer);

        $this->assertTrue($metadata->upgradeable);
        $this->assertTrue($metadata->readable);
        $this->assertTrue($metadata->payable);
        $this->assertTrue($metadata->payableBySc);
    }

    public function testShouldCreateFromBufferCorrectlyWhenSomeFlagsAreSet(): void
    {
        $buffer = chr(CodeMetadata::ByteZeroUpgradeable) .
                 chr(CodeMetadata::ByteOnePayableBySc);
        $metadata = CodeMetadata::fromBuffer($buffer);

        $this->assertTrue($metadata->upgradeable);
        $this->assertFalse($metadata->readable);
        $this->assertFalse($metadata->payable);
        $this->assertTrue($metadata->payableBySc);
    }

    public function testShouldHandleBufferTooShortError(): void
    {
        $buffer = chr(CodeMetadata::ByteZeroUpgradeable);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('buffer too short');

        CodeMetadata::fromBuffer($buffer);
    }

    public function testShouldTestCodeMetadataFromBytes(): void
    {
        $bytes = "\x01\x00";
        $codeMetadata = CodeMetadata::fromBuffer($bytes);

        $this->assertEquals('0100', $codeMetadata->toHex());
        $this->assertEquals(
            [
                'upgradeable' => true,
                'readable' => false,
                'payable' => false,
                'payableBySc' => false,
            ],
            json_decode($codeMetadata->toJSON(), true)
        );
    }
}
