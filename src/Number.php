<?php declare(strict_types=1);

namespace ShopblocksEngineering\BinarySafeCalculations;

final class Number
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @param string $number
     */
    public function __construct(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getDecimals(): string
    {
        return explode('.', $this->number)[1] ?? "";
    }

    /**
     * @return string
     */
    public function getIntegers(): string
    {
        return explode('.', $this->number)[0];
    }

    /**
     * Safely multiply by the provided Number
     *
     * @param Number $multiplier
     * @return $this
     */
    function multiply(self $multiplier): self
    {
        [$subject, $multiplier, $maxDecimalCount] = $this->toIntegers($multiplier);
        $multipliedNumber = new self((string) ($subject * $multiplier));
        return $multipliedNumber->moveDecimalPoint(-$maxDecimalCount * 2);
    }

    /**
     * Safely divide by the provided Number
     * @param Number $divisor
     * @return $this
     * @throws DivisionByZeroException
     */
    function divide(self $divisor): self
    {
        if ($divisor->getNumber() === "0") {
            throw new DivisionByZeroException("You cannot divide by Zero.");
        }
        [$subject, $divisor, $maxDecimalCount] = $this->toIntegers($divisor);

        $subject = (new self((string) $subject))->getNumber();
        $dividedNumber = new self((string) ($subject / $divisor));
        return $dividedNumber->moveDecimalPoint($maxDecimalCount);
    }

    /**
     * Safely add the provided Number
     * @param Number $addition
     * @return $this
     */
    public function add(self $addition): self
    {
        [$subject, $addition, $maxDecimalCount] = $this->toIntegers($addition);
        $multipliedNumber = new self((string) ($subject + $addition));
        return $multipliedNumber->moveDecimalPoint(-$maxDecimalCount);
    }

    /**
     * Safely subtract the provided Number
     * @param Number $subtraction
     * @return $this
     */
    public function subtract(self $subtraction): self
    {
        [$subject, $subtraction, $maxDecimalCount] = $this->toIntegers($subtraction);
        $multipliedNumber = new self((string) ($subject - $subtraction));
        return $multipliedNumber->moveDecimalPoint(-$maxDecimalCount);
    }

    /**
     * @param int $spaces
     * @return $this
     */
    protected function moveDecimalPoint(int $spaces): self
    {
        if ($spaces === 0) {
            return $this;
        }
        $value = $this->number;
        $parts = explode('.', $value);
        $parts[1] = $parts[1] ?? "";
        if ($spaces > 0) {
            $partsZeroLength = strlen($parts[0]);
            $parts[0] = "$parts[0]" . substr($parts[1], 0, $spaces);
            if (strlen($parts[0]) !== ($partsZeroLength + $spaces)) {
                $zeros = ($partsZeroLength + $spaces) - strlen($parts[0]);
                $parts[0] = "$parts[0]". str_repeat("0", $zeros);
            }
            $parts[1] = substr($parts[1], $spaces);
        } else {
            $parts[1] = substr($parts[0], strlen($parts[0]) - abs($spaces));
            $parts[0] = substr($parts[0], 0, strlen($parts[0]) - abs($spaces));
            if (empty($parts[0])) {
                $parts[0] = 0;
            }

            if ($this->onlyZeros($parts[1])) {
                return new self($parts[0]);
            }
        }

        return new self(implode('.', $parts));
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function onlyZeros(string $value): bool
    {
        foreach (str_split($value) as $character) {
            if ($character !== "0") {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Number $number
     * @return array
     */
    protected function toIntegers(self $number): array
    {
        $decimalCount = strlen($this->getDecimals());
        $numberDecimalCount = strlen($number->getDecimals());
        $maxDecimalCount = max($decimalCount, $numberDecimalCount);
        $subject = (int) $this->moveDecimalPoint($maxDecimalCount)->getNumber();
        $secondarySubject = (int) $number->moveDecimalPoint($maxDecimalCount)->getNumber();
        return [$subject, $secondarySubject, $maxDecimalCount];
    }

    /**
     * Return the current value of the Number
     *
     * @return string
     */
    public function getNumber(int $precision = 0): string
    {
        if (!$precision) {
            return $this->number;
        }
        $decimals = $this->getDecimals();
        if (strlen($decimals) === $precision) {
            return (string) round((float) $this->number, $precision);
        }
        $number = (string) round((float) "{$this->getIntegers()}.$decimals", $precision);
        $zerosToAdd = str_repeat("0", abs($precision - strlen(explode('.', $number)[1] ?? "")));
        if (strpos($number, '.') !== false) {
            return $number.$zerosToAdd;
        }

        return "$number.$zerosToAdd";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getNumber();
    }
}