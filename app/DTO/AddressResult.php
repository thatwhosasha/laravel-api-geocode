<?php

namespace App\DTO;

 class AddressResult
{
    public function __construct(
        public string $fullAddress,
        public string $district = 'Не указан',
        public string $metro   = 'Не указано',
        public string $street  = '',
        public string $house   = '',
    ) {}
}
