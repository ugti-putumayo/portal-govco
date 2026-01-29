<div class="search-container mb-3">
    <label for="show">Mostrar</label>
    <select id="show" class="form-select form-select-sm" style="width: auto;">
        <option>10</option>
        <option>20</option>
        <option>50</option>
    </select>
    <div class="input-group">
        <input type="text" class="form-control form-control-sm" placeholder="Buscar..." aria-label="Buscar">
        <button class="btn btn-primary btn-sm" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Nombres</th>
            <th>Cargo</th>
            <th>Dependencia</th>
            <th>Código</th>
            <th>Grado</th>
            <th>Nivel</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{ $item->fullname }}</td>
                <td>{{ $item->charge }}</td>
                <td>{{ $item->dependency }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ $item->grade }}</td>
                <td>{{ $item->level }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Paginación -->
@if ($data->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $data->appends(request()->input())->links('vendor.pagination.custom') }}
</div>
@endif
