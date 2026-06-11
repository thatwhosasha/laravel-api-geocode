<?php

namespace App\Services;
use App\DTO\AddressResult;
use Illuminate\Http\Client\Factory;
use App\Exceptions\GeocoderException;

class YandexGeocoder implements GeocoderInterface
{
    private Factory $http;
    private string $apiKey;

    public function __construct(Factory $http, string $apiKey) {
        $this->http = $http;
        $this->apiKey = $apiKey;
    }

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
            throw new GeocoderException( 'Ошибка запроса к геокодеру' . $response->body());
        }

        $data = $response->json();

        // Проверяем ошибку внутри JSON ответа
        if (isset($data['error'])) {
            \Log::warning('Yandex geocoder HTTP error', ['body' => $response->body(), 'status' => $response->status()]);
            throw new GeocoderException('Ошибка запроса к геокодеру: ' . $response->status());
        }

        // Извлекаем коллекцию объектов
        $featureMembers = $data['response']['GeoObjectCollection']['featureMember'] ?? [];
        if (empty($featureMembers)) {
            return [];
        }
        $results = [];

        // Обход каждого элемента и фильтрация по Москве
        foreach ($featureMembers as $featureMember) {
            $geoObject = $featureMember['GeoObject'] ?? null;
            if (!$geoObject) {
                continue;
            }

            $metaData = $geoObject['metaDataProperty']['GeocoderMetaData'] ?? [];

            $components = $metaData['Address']['Components'] ?? [];

            if(!$this->isMoscow( $components)) {
                continue;
            }

            $extracted = $this->extractFromComponents($components);

            $fullAddress = $metaData['text'] ?? $geoObject['name'] ?? '';

            $results[] = new AddressResult([
                'fullAddress' => $fullAddress,
                 'district' => $extracted['district'],
                 'metro' => $extracted['metro'],
                 'street' => $extracted['street'],
                 'house' => $extracted['house'],
            ]);

        }

        return $results;

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


