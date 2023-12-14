<?php

if (! function_exists('getStoreRequest')) {
    /**
     * Identifica qual loja fez a requisição.
     * Se for 'null' é requisição do seller center.
     *
     * @return  int|null
     */
    function getStoreRequest(): int|null
    {
        $store = request()->route('store');

        if ($store === 'null') {
            return null;
        }

        return (int)$store;
    }
}

if (! function_exists('getTenantRequest')) {
    /**
     * Identifica qual o tenant que fez a requisição.
     *
     * @return  string|null
     */
    function getTenantRequest(): string|null
    {
        try {
            $jwt = request()->headers->get('Authorization', '') ?? '';
            $jwt = explode('.', $jwt);
            $payload = json_decode(base64_decode($jwt[1] ?? base64_encode('{}')));
            $tenant = $payload->client_id ?? $payload->clientId ?? '';
            if (!empty($tenant)) return $tenant;

            $tenant = request()->route('tenant', '') ?? '';
            if (!empty($tenant)) return $tenant;
            $token = base64_decode(request()->route('token') ?? '');
            $tokenExplode = explode(':', $token);
            return $tokenExplode[1] ?? '';
        } catch (\Throwable $e) {
        }
        return '';
    }
}

if (! function_exists('getAllPramsRequest')) {
    /**
     * Recupera os parâmetros da requisição.
     *
     * @return  array
     */
    function getAllPramsRequest(): array
    {
        return [
            'tenant' => getTenantRequest(),
            'store'  => getStoreRequest()
        ];
    }
}

if (! function_exists('getPlatformRequest')) {
    /**
     * Identifica qual a plataforma que fez a requisição.
     *
     * @return  string
     */
    function getPlatformRequest(): ?string
    {
        return request()->route('platform') ?? null;
    }
}

if (! function_exists('getMakretplaceRequest')) {
    /**
     * Identifica qual o marketplace que fez a requisição.
     *
     * @return  string
     */
    function getMakretplaceRequest(): ?string
    {
        return getMarketplaceRequest() ?? null;
    }
}

if (! function_exists('getMarketplaceRequest')) {
    /**
     * Identifica qual o marketplace que fez a requisição.
     *
     * @return  string
     */
    function getMarketplaceRequest(): ?string
    {
        return request()->route('marketplace') ?? null;
    }
}

if (! function_exists('onlyNumbers')) {
    /**
     * Limpa a string para manter apenas números.
     *
     * @param   string|null $value
     * @param   string|null $padding
     * @return  string|null
     */
    function onlyNumbers(?string $value, ?string $padding = 'NA'): ?string
    {
        if ($value === null) {
            return null;
        }

        $result = preg_replace('/\D/', '', $value);

        if ($padding == 'zip_code') {
            $result = str_pad($result, 8, 0, STR_PAD_LEFT);
        }

        return $result;
    }
}

if (! function_exists('formatNumber')) {
    function formatNumber($num, $padrao = "US"): array|bool|string
    {   // Ou BR
        $num = preg_replace("/[^0-9^.^,]/", "", trim($num));
        $temp = str_replace(",", "", $num);
        $temp = str_replace(".", "", $temp);
        if (is_numeric($temp)) {
            $num = str_replace(",", ".", $num);
            $ct = false;
            while (!$ct) {
                $temp = str_replace(".", "", $num, $cnt);
                if ($cnt < 2) {
                    $ct = true;
                } else {
                    $pos = strpos($num, ".");
                    $num = substr($num, 0, $pos).substr($num, $pos + 1);
                    $ct = false;
                }
            }
            return $num;
        } else {
            return false;
        }
    }
}

if (!function_exists('roundDecimal')) {
    function roundDecimal(float $price, int $decimal = 2): float
    {
        return (float)number_format($price, $decimal, '.', '');
    }
}

if (!function_exists('moneyFloatToVtex')) {
    function moneyFloatToVtex(float $price): int
    {
        // Garantir apenas duas casas decimais
        $price = number_format($price, 2, '.', '');

        // Multiplicar por 100 para deixa o preço sem decimal. 199.98 => 19998
        return (int)($price * 100);
    }
}

if (!function_exists('moneyVtexToFloat')) {
    function moneyVtexToFloat(int $price): float
    {
        return (float)($price / 100);
    }
}

if (!function_exists('moneyToFloat')) {
    function moneyToFloat(string $price): float
    {
        $price = trim(str_replace('R$', '', $price));
        $price = str_replace('.', '', $price);
        $price = str_replace(',', '.', $price);

        return (float)$price;
    }
}

if (!function_exists('getArrayByValueIn')) {
    function getArrayByValueIn(?array $array, string $fieldValidate, string $fieldArray)
    {
        if ($array === null) {
            return array();
        }

        return current(array_filter($array, function($item) use ($fieldValidate, $fieldArray) {
            if (($item->$fieldArray ?? $item[$fieldArray]) === $fieldValidate) {
                return true;
            }
            return false;
        }));
    }
}

if (!function_exists('likeText')) {
    function likeText(string $needle, string $haystack): bool
    {
        $regex = '/' . str_replace('%', '.*?', $needle) . '/';

        return preg_match($regex, $haystack) > 0;
    }
}

if (! function_exists('detectUTF8')) {
    function detectUTF8(string $input): string
    {
        $detect = preg_match('%(?:
            [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
            )+%xs', $input);

        return $detect == 1 ? $input : utf8_encode($input);
    }
}

if (!function_exists('mask')) {
    function mask($val, $mask): string
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i])) $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
}

if (!function_exists('formatCpf')) {
    function formatCpf(string $val): string
    {
        $val = onlyNumbers($val);
        return mask($val, '###.###.###-##');
    }
}

if (!function_exists('formatCnpj')) {
    function formatCnpj(string $val): string
    {
        $val = onlyNumbers($val);
        return mask($val, '##.###.###/####-##');
    }
}

if (! function_exists('getStorageVariables')) {
    function getStorageVariables(): array|string
    {
        $directory = "";
        if (config("app.storage_directory") != null) {
            $directory = config("app.storage_directory");
        }

        $s3_directory = "";
        if (config("app.s3_directory") != null) {
            $s3_directory = config("app.s3_directory");
        }

        $aws_access_key = "";
        if (config("app.aws_access_key") != null) {
            $aws_access_key = config("app.aws_access_key");
        }

        $aws_secret_key = "";
        if (config("app.aws_secret_key") != null) {
            $aws_secret_key = config("app.aws_secret_key");
        }

        $bucket_name = "";
        if (config("app.bucket_name") != null) {
            $bucket_name = config("app.bucket_name");
        }

        $result = json_encode(array('status' => 'fail'));
        if (
            !empty($directory) &&
            !empty($s3_directory) &&
            !empty($aws_access_key) &&
            !empty($aws_secret_key) &&
            !empty($bucket_name)
        ) {
            $result = json_encode(
                array(
                    'status' => 'success',
                    'directory' => $directory,
                    's3_directory' => $s3_directory,
                    'aws_access_key' => $aws_access_key,
                    'aws_secret_key' => $aws_secret_key,
                    'bucket_name' => $bucket_name
                )
            );
        }

        return $result;
    }
}
