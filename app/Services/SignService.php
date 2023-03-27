<?php

namespace App\Services;

class SignService
{
    public function sign(array $array, string $api_key): array
    {
        $this->clearEmptyValue($array);
        $this->kSortRecursive($array);
        $array['api_key'] = $api_key;
        $sign = md5(urldecode(http_build_query($array)));
        $array['sign'] = $sign;
        unset($array['api_key']);
        return $array;
    }

    function clearEmptyValue(array &$array)
    {
        array_walk_recursive($array, function (&$v) {
            $v = is_string($v) && trim($v) === '' ? null : $v;
        });
    }

    /**
     * @param $array
     * @return bool
     */
    function kSortRecursive(&$array): bool
    {
        if (!is_array($array)) {
            return false;
        }

        ksort($array);
        foreach ($array as $k => $v) {
            $this->ksortRecursive($array[$k]);
        }
        return true;
    }
}