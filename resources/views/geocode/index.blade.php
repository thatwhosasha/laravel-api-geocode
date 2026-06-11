<style>
    body {
        font-family: sans-serif;
        max-width: 960px;
        margin: 48px auto;
        padding: 0 20px;
        color: #222;
    }

    h1 {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .search-form {
        display: flex;
        gap: 8px;
        margin-bottom: 28px;
    }

    .search-form input {
        flex: 1;
        padding: 9px 14px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        outline: none;
    }

    .search-form input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59,130,246,.2);
    }

    .search-form button {
        padding: 9px 22px;
        font-size: 1rem;
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        white-space: nowrap;
    }

    .search-form button:hover {
        background: #2563eb;
    }

    .error {
        color: #dc2626;
        margin-bottom: 16px;
    }

    .not-found {
        color: #666;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    thead th {
        text-align: left;
        padding: 10px 14px;
        background: #f3f4f6;
        border-bottom: 2px solid #e5e7eb;
        color: #555;
        font-weight: 600;
    }

    tbody td {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    tbody tr:hover {
        background: #f9fafb;
    }
</style>

<h1>Поиск адреса в Москве</h1>

<form class="search-form" action="{{ route('geocode.search') }}" method="POST">
    @csrf
    <input
        type="text"
        name="address"
        value="{{ old('address', $address ?? '') }}"
        placeholder="Например: Тверская улица, 1"
        required
        minlength="3"
    >
    <button type="submit">Найти</button>
</form>

@if(!empty($error))
    <p class="error">{{ $error }}</p>
@endif

@if(isset($results))
    @if(count($results) > 0)
        <table>
            <thead>
            <tr>
                <th>Адрес</th>
                <th>Район</th>
                <th>Метро</th>
                <th>Улица</th>
                <th>Дом</th>
            </tr>
            </thead>
            <tbody>
            @foreach($results as $item)
                <tr>
                    <td>{{ $item->fullAddress }}</td>
                    <td>{{ $item->district }}</td>
                    <td>{{ $item->metro }}</td>
                    <td>{{ $item->street }}</td>
                    <td>{{ $item->house }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p class="not-found">По данному запросу ничего не найдено в Москве.</p>
    @endif
@endif
