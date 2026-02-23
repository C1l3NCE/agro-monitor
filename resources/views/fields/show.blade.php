@extends('layouts.admin')

@section('header', 'Информация о поле')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-2">{{ $field->name }}</h2>

    <p><strong>Культура:</strong> {{ $field->crop }}</p>
    <p><strong>Площадь:</strong> {{ $field->area }} га</p>
    <p><strong>NDVI:</strong> {{ $field->ndvi_avg ?? '—' }}</p>
</div>
@endsection
