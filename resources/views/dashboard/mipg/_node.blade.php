<li data-id="{{ $file->id }}" data-path="{{ $file->path }}">
    @if ($file->type === 'directory')
        <div onclick="toggleFolder(this)" class="directory" ondblclick="enableRename(this, {{ $file->id }})">
            📁 <span class="filename">{{ $file->name }}</span>
        </div>
        @if(isset($file->children) && $file->children->count())
            <ul class="file-tree d-none">
                @foreach ($file->children as $child)
                    @include('dashboard.mipg._node', ['file' => $child])
                @endforeach
            </ul>
        @endif
    @else
        @php
            $extension = strtolower($file->extension ?? '');
            switch ($extension) {
                case 'pdf':
                    $icon = asset('icon/pdf.png'); break;
                case 'doc':
                case 'docx':
                    $icon = asset('icon/word.png'); break;
                case 'xls':
                case 'xlsx':
                    $icon = asset('icon/excel.png'); break;
                case 'pptx':
                    $icon = asset('icon/powerpoint.png'); break;
                default:
                    $icon = asset('icon/default.png'); break;
            }
        @endphp

        <div class="file" ondblclick="enableRename(this, {{ $file->id }})">
            <img src="{{ $icon }}" alt="{{ $extension }}" style="width: 18px; margin-right: 5px;">
            <a href="{{ asset('storage/' . $file->file) }}" target="_blank">
                <span class="filename">{{ $file->name }}</span>
            </a>
            @if(isset($file->is_visible))
                <span class="badge-status {{ $file->is_visible ? 'active' : 'inactive' }}" data-id="visibility-{{ $file->id }}">
                    {{ $file->is_visible ? 'Activo' : 'Inactivo' }}
                </span>
            @endif
        </div>
    @endif
</li>
<style>
.badge-status {
    margin-left: 10px;
    padding: 2px 6px;
    font-size: 0.75rem;
    font-weight: bold;
    border-radius: 4px;
}
.badge-status.active {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.badge-status.inactive {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>