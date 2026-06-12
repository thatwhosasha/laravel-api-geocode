<?php

namespace App\Services;
use App\DTO\AddressResult;
use Illuminate\Http\Client\Factory;
use App\Exceptions\GeocoderException;
use Illuminate\Support\Facades\Log;

class YandexGeocoder implements GeocoderInterface
{
    public function __construct(
        private Factory $http,
        private string $apiKey,
    ){}

    public function search(string $address): array
    {
        $response = $this->http->timeout(5)->get('https://geocode-maps.yandex.ru/v1/', [
            'apikey' => $this->apiKey,
            'geocode' => $address,
            'format' => 'json',
            'results'=> 5,
            'lang' => 'ru_RU',

        ]);

        // Ошибка HTTP
        if ($response->failed()) {
            Log::warning('Ошибка HTTP-запроса к геокодеру', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new GeocoderException('Ошибка соединения с геокодером: ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['error'])) {
            $message = $data['error']['message'] ?? 'Неизвестная ошибка';
            Log::warning('Ошибка API Яндекс геокодера', [
                'message' => $message,
            ]);
            throw new GeocoderException('Ошибка геокодера: ' . $message);
        }

        $featureMembers = $data['response']['GeoObjectCollection']['featureMember'] ?? [];

        return array_values(array_filter(
            array_map([$this, 'parseFeatureMember'], $featureMembers)
        ));
    }

    private function parseFeatureMember(array $featureMember): ?AddressResult
    {
        $geoObject = $featureMember['GeoObject'] ?? null;
        if (!$geoObject) {
            return null;
        }

        $metaData = $geoObject['metaDataProperty']['GeocoderMetaData'] ?? [];
        $components = $metaData['Address']['Components'] ?? [];

        if (!$this->isMoscow($components)) {
            return null;
        }

        $extracted = $this->extractFromComponents($components);

        return new AddressResult(
            fullAddress: $metaData['text'] ?? $geoObject['name'] ?? '',
            district: $extracted['district'] ?? 'Не указан',
            metro: $extracted['metro']    ?? 'Не указано',
            street: $extracted['street'],
            house: $extracted['house'],
        );
    }

    private function isMoscow( array $components): bool
    {
        foreach ($components as $component) {
            $kind = $component['kind'] ?? '';
            $name = $component['name'] ?? '';
            if (in_array($kind, ['locality', 'province', 'area']) && mb_stripos($name, 'Москва') !== false) {
                return true;
            }
        }
        return false;
    }

    private function extractFromComponents(array $components): array
    {
        $results = [
            'district' => null,
            'metro' => null,
            'street' => '',
            'house' => '',
        ];

        foreach ($components as $component)
        {
            $kind = $component['kind'] ?? '';
            $name = $component['name'] ?? '';

            switch ($kind) {
                case 'district':
                    if ($results['district'] === null) {
                        $results['district'] = $name;
                    }
                    break;
                case 'metro':
                    if ($results['metro'] === null) {
                        $results['metro'] = $name;
                    }
                    break;
                case 'street':
                    $results['street'] = $name;
                    break;
                case 'house':
                    $results['house'] = $name;
                    break;
            }
        }

        return $results;
    }
}


