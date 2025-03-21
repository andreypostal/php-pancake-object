<?php /** @noinspection ForeachInvariantsInspection */

namespace Andrey\PancakeObject\KeyMapping;

/**
 *
 */
class KeyMappingUnderscore implements KeyMappingStrategy
{
    /**
     * Map keys from pascalCase to underscore_case
     *
     */
    public function from(string $key): string
    {
        $in = str_split($key);
        $len = count($in);
        $out = '';
        for ($i = 0; $i < $len; $i++) {
            if ($in[$i] < 'a') {
                $out .= '_';
                // if already is an underscore, just skip case conversion
                // but still add another underscore before it
                if ($in[$i] !== '_') {
                    $out .= chr((ord($in[$i]) - ord('A')) + ord('a'));
                } else {
                    $out .= $in[$i];
                }
            } else {
                $out .= $in[$i];
            }
        }
        return $out;
    }

    public function to(string $key): string
    {
        $in = str_split($key);
        $len = count($in);
        $out = '';
        // my_key => myKey
        for ($i = 0; $i < $len; $i++) {
            $c = $in[$i];
            if ($c === '_') {
                $c = $in[$i+1];
                // jump to next letter (skip lowercase already dealt with)
                $i++;
                if ($c !== '_') {
                    $out .= chr((ord($c) - ord('a')) + ord('A'));
                } else {
                    $out .= $c;
                }
            } else {
                $out .= $c;
            }
        }

        return $out;
    }
}
