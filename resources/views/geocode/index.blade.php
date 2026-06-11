<form action="{{ route('geocode.search') }}" method="POST">
    @csrf
    <input type="text" name="address" value="{{ old('address', $address ?? '') }}" required minlength="3">
    <button type="submit">Найти</button>
</form>

@if(!empty($error))
    <div style="color:red;">{{ $error }}</div>

@endif

@if(!empty($results))
    @if(count($results))
        <table>
            <tr><th>Адрес</th><th>Район</th><th>Метро</th><th>Улица</th><th>Дом</th></tr>
            @foreach($results as $item)
                <tr>
                    <td>{{ $item->getFullAddress() }}</td>
                    <td>{{ $item->getDistrict() }}</td>
                    <td>{{ $item->getMetro() }}</td>
                    <td>{{ $item->getStreet() }}</td>
                    <td>{{ $item->getHouse() }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>Ничего не найдено в Москве.</p>
    @endif
@endif
