<?php

namespace App\Services;

interface GeocoderInterface
{
    public function search(string $address): array;
}
