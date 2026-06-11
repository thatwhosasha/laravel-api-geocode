<?php

namespace App\Http\Controllers;

//use App\Models\SearchQuery;
use App\Repositories\QueryRepository;
use App\Exceptions\GeocoderException;
use App\Services\GeocoderInterface;
use Illuminate\Http\Request;


class GeocodeController extends Controller
{
    private GeocoderInterface $geocoder;
    private QueryRepository $queryRepository;

    public function __construct(GeocoderInterface $geocoder, QueryRepository $queryRepository)
    {
        $this->geocoder = $geocoder;
        $this->queryRepository = $queryRepository;
    }

    public function index()
    {
        return view('geocode.index');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|min:3',
        ]);

        $address = trim($validated['address']);

        $normalized = preg_replace('/\s+/', ' ', $address);
        $normalized = mb_strtolower($normalized, 'UTF-8');
        $normalized = mb_substr($normalized, 0, 255);

        try {
            $results = $this->geocoder->search($address);

//            SearchQuery::firstOrCreate(['query' => $normalized]);
            $this->queryRepository->saveUnique($normalized);

            return view('geocode.index', [
                'results' => $results,
                'address' => $address,
            ]);
        } catch (GeocoderException $e) {
            return view('geocode.index', [
                'error' => $e->getMessage(),
                'address' => $address,
            ]);
        } catch (\Exception $e) {
            \Log::error('GeocodeController error', ['exception' => $e]);
            return view('geocode.index', [
                'error' => 'Произошла внутрення ошибка.',
                'address' => $address,
            ]);
        }
    }
}
