<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait DotEnv
{
    public function editEnv($key, $value)
    {
        $env = file_get_contents(base_path('.env'));
        $parsed = $this->parse($key, $value);

        if (is_bool($value)) {
            $newText = str_replace($key.'='.(env($key) ? 'true' : 'false'), $parsed, $env);
        } else {
            $newText = str_replace($key.'='.env($key), $parsed, $env);
        }

        if (env($key) === null) {
            $newText = $newText.$parsed."\n";
        }

        File::put(base_path('.env'), $newText);

        return true;
    }

    private function parse($key, $value)
    {
        return is_bool($value) ? $this->parseBool($key, $value) : $this->parseString($key, $value);
    }

    private function parseBool($key, $value)
    {
        return $key.'='.($value ? 'true' : 'false');
    }

    private function parseString($key, $value)
    {
        return $key.'='.preg_replace('/\s+/', '', $value);
    }
}
