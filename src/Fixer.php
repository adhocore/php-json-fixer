<?php

namespace Ahc\Json;

/**
 * Attempts to fix truncated JSON by padding contextual counterparts at the end.
 *
 * @author  Jitendra Adhikari <jiten.adhikary@gmail.com>
 * @license MIT
 *
 * @link    https://github.com/adhocore/php-json-fixer
 */
class Fixer
{
    use PadsJson;

    /** @var array Current token stack indexed by position */
    protected $stack = [];

    /** @var bool If current char is within a string */
    protected $inStr = false;

    /** @var array The complementary pairs */
    protected $pairs = [
        '{' => '}',
        '[' => ']',
        '"' => '"',
    ];

    /** @var int The last seen object `{` type position */
    protected $objectPos = -1;

    /** @var int The last seen array `[` type position */
    protected $arrayPos  = -1;

    /** @var string Missing value. (Options: true, false, null) */
    protected $missingValue = 'true';

    /**
     * Fix the truncated JSON.
     *
     * @param string $json         The JSON string to fix.
     * @param bool   $silent       If silent, doesnt throw when fixing fails.
     * @param string $missingValue Missing value constructor. (Options: true, false, null).
     *
     * @throws \RuntimeExcaption When fixing fails.
     *
     * @return string Fixed JSON. If failed with silent then original JSON.
     */
    public function fix($json, $silent = false, $missingValue = 'null')
    {
        list($head, $json, $tail) = $this->trim($json);

        if (empty($json) || $this->isValid($json)) {
            return $json;
        }

        if (null !== $tmpJson = $this->quickFix($json)) {
            return $tmpJson;
        }

        $this->reset($missingValue);

        return $head . $this->doFix(\rtrim($json), $silent) . $tail;
    }

    public function trim($json)
    {
        \preg_match('/^(\s+)([^\s]+)(\s+)$/', $json, $match);

        $match += ['', '', \trim($json), ''];

        \array_shift($match);

        return $match;
    }

    protected function isValid($json)
    {
        \json_decode($json);

        return \JSON_ERROR_NONE === \json_last_error();
    }

    public function quickFix($json)
    {
        if (\strlen($json) === 1 && isset($this->pairs[$json])) {
            return $json . $this->pairs[$json];
        }

        if ($json[0] !== '"') {
            return $this->maybeLiteral($json);
        }

        return $this->padString($json);
    }

    protected function maybeLiteral($json)
    {
        if (!\in_array($json[0], ['t', 'f', 'n'])) {
            return null;
        }

        foreach (['true', 'false', 'null'] as $literal) {
            if (\strpos($literal, $json) === 0) {
                return $literal;
            }
        }

        return null;
    }

    protected function reset($missingValue = 'null')
    {
        $this->stack = [];
        $this->inStr = false;

        $this->objectPos = -1;
        $this->arrayPos  = -1;

        $this->missingValue = $missingValue;
    }

    protected function doFix($json, $silent = false)
    {
        list($index, $char) = [-1, ''];

        while (isset($json[++$index])) {
            list($prev, $char) = [$char, $json[$index]];

            $next = isset($json[$index + 1]) ? $json[$index + 1] : '';

            if (!\in_array($char, [' ', "\n", "\r"])) {
                $this->stack($prev, $char, $index, $next);
            }
        }

        return $this->fixOrFail($json, $silent);
    }

    protected function stack($prev, $char, $index, $next)
    {
        if ($this->maybeStr($prev, $char, $index)) {
            return;
        }

        $last = $this->lastToken();

        if (\in_array($last, [',', ':', '"']) && \preg_match('/\"|\d|\{|\[|t|f|n/', $char)) {
            \array_pop($this->stack);
        }

        if (\in_array($char, [',', ':', '[', '{'])) {
            $this->stack[$index] = $char;
        }

        $this->updatePos($char, $index);
    }

    protected function lastToken()
    {
        return \end($this->stack);
    }

    protected function maybeStr($prev, $char, $index)
    {
        if ($prev !== '\\' && $char === '"') {
            $this->inStr = !$this->inStr;
        }

        if ($this->inStr && $this->lastToken() !== '"') {
            $this->stack[$index] = '"';
        }

        return $this->inStr;
    }

    protected function updatePos($char, $index)
    {
        if ($char === '{') {
            $this->objectPos = $index;
        } elseif ($char === '}') {
            $this->objectPos = -1;
        } elseif ($char === '[') {
            $this->arrayPos = $index;
        } elseif ($char === ']') {
            $this->arrayPos = -1;
        }
    }

    protected function fixOrFail($json, $silent)
    {
        $length  = \strlen($json);
        $tmpJson = $this->pad($json);

        if ($this->isValid($tmpJson)) {
            return $tmpJson;
        }

        if ($silent) {
            return $json;
        }

        throw new \RuntimeException(
            \sprintf('Couldnt fix JSON (tried padding `%s`)', \substr($tmpJson, $length))
        );
    }
}
