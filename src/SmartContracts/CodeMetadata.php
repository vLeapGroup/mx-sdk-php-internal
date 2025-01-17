<?php

namespace MultiversX\SmartContracts;

use Exception;

class CodeMetadata
{
    const ByteZeroUpgradeable = 1;

    const ByteZeroReserved2 = 2;

    const ByteZeroReadable = 4;

    const ByteOneReserved1 = 1;

    const ByteOnePayable = 2;

    const ByteOnePayableBySc = 4;

    public function __construct(
        public $upgradeable = true,
        public $readable = false,
        public $payable = false,
        public $payableBySc = false
    ) {}

    public static function fromBuffer($buffer): self
    {
        if (strlen($buffer) < 2) {
            throw new Exception('buffer too short');
        }

        $byteZero = ord($buffer[0]);
        $byteOne = ord($buffer[1]);

        $upgradeable = ($byteZero & self::ByteZeroUpgradeable) !== 0;
        $readable = ($byteZero & self::ByteZeroReadable) !== 0;
        $payable = ($byteOne & self::ByteOnePayable) !== 0;
        $payableBySc = ($byteOne & self::ByteOnePayableBySc) !== 0;

        return new self($upgradeable, $readable, $payable, $payableBySc);
    }

    public static function fromHex(string $hex): self
    {
        return self::fromBuffer(hex2bin($hex));
    }

    public function toggleUpgradeable(bool $value)
    {
        $this->upgradeable = $value;
    }

    public function toggleReadable(bool $value)
    {
        $this->readable = $value;
    }

    public function togglePayable(bool $value)
    {
        $this->payable = $value;
    }

    public function togglePayableBySc(bool $value)
    {
        $this->payableBySc = $value;
    }

    public function toBuffer()
    {
        $byteZero = 0;
        $byteOne = 0;

        if ($this->upgradeable) {
            $byteZero |= self::ByteZeroUpgradeable;
        }
        if ($this->readable) {
            $byteZero |= self::ByteZeroReadable;
        }
        if ($this->payable) {
            $byteOne |= self::ByteOnePayable;
        }
        if ($this->payableBySc) {
            $byteOne |= self::ByteOnePayableBySc;
        }

        return chr($byteZero).chr($byteOne);
    }

    public function toHex(): string
    {
        return bin2hex($this->toBuffer());
    }

    public function toJSON()
    {
        return json_encode([
            'upgradeable' => $this->upgradeable,
            'readable' => $this->readable,
            'payable' => $this->payable,
            'payableBySc' => $this->payableBySc,
        ]);
    }

    public function equals(CodeMetadata $other)
    {
        return $this->upgradeable == $other->upgradeable &&
               $this->readable == $other->readable &&
               $this->payable == $other->payable &&
               $this->payableBySc == $other->payableBySc;
    }
}
