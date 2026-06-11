<?php

namespace App\DTO;

class AddressResult
{
    private string $fullAddress;
    private string $district;
    private string $metro;
    private string $street;
    private string $house;
    public function __construct(array $data) {
        $this->fullAddress = $data['fullAddress'] ?? '';
        $this->district = $data['district'] ?? 'Не указан';
        $this->metro = $data['metro'] ?? 'Не указано';
        $this->street = $data['street'] ?? '';
        $this->house = $data['house'] ?? '';
    }

    public function getFullAddress(): string{
        return $this->fullAddress;
    }

    public function getDistrict(): string{
        return $this->district;
    }
    public function getMetro(): string{
        return $this->metro;
    }
    public function getStreet(): string{
        return $this->street;
    }
    public function getHouse(): string{
        return $this->house;
    }
}
