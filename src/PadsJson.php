<?php

namespace Ahc\Json;

/**
 * Attempts to fix truncated JSON by padding contextual counterparts at the end.
 *
 * @author   Jitendra Adhikari <jiten.adhikary@gmail.com>
 * @license  MIT
 *
 * @internal
 *
 * @link     https://github.com/adhocore/php-json-fixer
 */
trait PadsJson
{
    public function pad($tmpJson)
    {
        if (!$this->inStr) {
            $tmpJson = \rtrim($tmpJson, ',');
            while ($this->lastToken() === ',') {
                \array_pop($this->stack);
            }
        }

        $tmpJson = $this->padLiteral($tmpJson);
        $tmpJson = $this->padObject($tmpJson);

        return $this->padStack($tmpJson);
    }

    protected function padLiteral($tmpJson)
    {
        if ($this->inStr) {
            return $tmpJson;
        }

        $match = \preg_match('/(tr?u?e?|fa?l?s?e|nu?l?l?)$/', $tmpJson, $matches);

        if (!$match || null === $literal = $this->maybeLiteral($matches[1])) {
            return $tmpJson;
        }

        return \substr($tmpJson, 0, 0 - \strlen($matches[1])) . $literal;
    }

    protected function padStack($tmpJson)
    {
        foreach (\array_reverse($this->stack, true) as $index => $token) {
            if (isset($this->pairs[$token])) {
                $tmpJson .= $this->pairs[$token];
            } elseif (\in_array($token, [':', ','])) {
                $tmpJson .= $this->padValue($tmpJson, $token, $index);
            }
        }

        return $tmpJson;
    }

    protected function padObject($tmpJson)
    {
        $empty = \substr($tmpJson, -1) == '{' && !$this->inStr;

        if ($empty || $this->arrayPos > $this->objectPos) {
            return $tmpJson;
        }

        $part = \substr($tmpJson, $this->objectPos + 1);

        if (\preg_match('/(,)?(\"[^"]+\"(\s*:\s*)?[^,]*)+$/', $part, $matches)) {
            return $tmpJson;
        }

        $tmpJson .= $this->inStr ? '":' : ':';
        $tmpJson .= $this->missingValue;
        if ($this->lastToken() === '"') {
            \array_pop($this->stack);
        }

        return $tmpJson;
    }

    protected function padValue($tmpJson, $token, $index)
    {
        if ($token === ':' && !$this->inStr && \substr($tmpJson, -1) === ':') {
            return $this->missingValue;
        }

        return '';
    }
}
