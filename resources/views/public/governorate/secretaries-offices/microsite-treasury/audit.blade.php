@if(isset($audits) && count($audits) > 0)
    <div class="row">
        <div class="col-md-6">
            <div class="list-group">
                @foreach($audits->take(5) as $audit)
                    <a href="{{ asset('storage/' . $fiscalizacion->archivo) }}" target="_blank" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-pdf text-danger"></i> {{ $audit->title }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <div class="list-group">
                @foreach($audits->slice(5) as $audit)
                    <a href="{{ asset('storage/' . $fiscalizacion->archivo) }}" target="_blank" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-pdf text-danger"></i> {{ $audit->title }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@else
    <p class="text-center text-muted">No hay registros de fiscalización disponibles.</p>
@endif




