@extends('dashboard.dashboard')
@push('scripts')
<script>
function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      || document.querySelector('input[name="_token"]')?.value;
}

async function deleteSlider(id) {
  const ok = await Confirm.open({
    title: 'Eliminar imagen del slider',
    message: 'Esta acción no se puede deshacer. ¿Deseas continuar?',
    confirmText: 'Eliminar',
    cancelText: 'Cancelar',
    danger: true
  });
  if (!ok) return;

  try {
    const res  = await fetch(`/dashboard/slider/images/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': getCsrf(),
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json().catch(() => ({}));
    if (res.ok) {
      Toast.success(data.message || 'Imagen eliminada con éxito.');
      setTimeout(() => location.reload(), 900);
    } else {
      console.error('Error al eliminar:', data);
      Toast.error(data.message || 'No se pudo eliminar la imagen.');
    }
  } catch (err) {
    console.error('Error inesperado:', err);
    Toast.error('Hubo un problema al eliminar la imagen.');
  }
}

function editSlider(id) {
  Toast.info(`Editar imagen #${id}`, { title: 'Edición' });
}

async function toggleStatus(id) {
  try {
    const res  = await fetch(`/dashboard/slider/images/${id}/toggle-status`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': getCsrf(),
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      console.error('Error al cambiar estado:', data);
      Toast.error(data.message || 'No se pudo actualizar el estado.');
      return;
    }

    const button = document.getElementById(`status-btn-${id}`);
    const label  = document.getElementById(`status-label-${id}`);

    if (button && label) {
      button.textContent = data.status ? 'Desactivar' : 'Activar';
      button.classList.toggle('btn-active',   data.status);
      button.classList.toggle('btn-inactive', !data.status);

      label.textContent = data.status ? 'Activo' : 'Inactivo';
      label.classList.toggle('text-success', data.status);
      label.classList.toggle('text-danger', !data.status);
    }

    Toast.success(data.message || 'Estado actualizado.');
  } catch (err) {
    console.error('Error al cambiar estado:', err);
    Toast.error('No se pudo actualizar el estado.');
  }
}


function openModalSlider() {
  document.getElementById('modalCreateSlider').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', () => {
  const items = document.querySelectorAll('.slider-item');
  let draggedItem = null;

  items.forEach(item => {
    item.setAttribute('draggable', true);

    item.addEventListener('dragstart', function () {
      draggedItem = this;
      this.classList.add('dragging');
    });

    item.addEventListener('dragend', function () {
      this.classList.remove('dragging');
    });

    item.addEventListener('dragover', function (e) {
      e.preventDefault();
    });

    item.addEventListener('drop', function (e) {
      e.preventDefault();
      if (draggedItem && draggedItem !== this) {
        const parent = this.parentNode;
        parent.insertBefore(draggedItem, this);
        updateOrder()
      }
    });
  });

  async function updateOrder() {
    const list = Array.from(document.querySelectorAll('.slider-item'));
    for (let i = 0; i < list.length; i++) {
      const item = list[i];
      const id   = item.dataset.id;
      try {
        const res  = await fetch(`/dashboard/slider/images/${id}/order`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': getCsrf(),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ order: i + 1 })
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok) {
          const badge = item.querySelector('.order-number');
          if (badge) badge.textContent = i + 1;
        } else {
          console.error('Error actualizando orden:', data);
        }
      } catch (err) {
        console.error('Error actualizando orden:', err);
      }
    }
    Toast.success('Orden actualizado.');
  }
});

async function openModalEditSlider(id) {
  try {
    const res  = await fetch(`/dashboard/slider/images/${id}/edit`);
    const data = await res.json();
    document.getElementById('edit_id').value    = data.id ?? '';
    document.getElementById('edit_title').value = data.title || '';
    document.getElementById('edit_link').value  = data.link  || '';

    const previewImg = document.getElementById('preview_image');
    if (previewImg && data.route) {
      previewImg.src = `/img/sliders/${data.route}`;
      previewImg.style.display = 'block';
    }
    document.getElementById('modalEditSlider').style.display = 'flex';
  } catch (err) {
    Toast.error('No se pudo cargar la información del slider.');
  }
}
</script>
@endpush

@section('content')
<div class="container">
    <h2>Gestión de Imágenes del Slider</h2>

    <div id="slider-list" class="slider-list mt-4">
        @foreach ($slider as $image)
            <div class="slider-item" data-id="{{ $image->id }}">
                <img src="{{ asset($image->route) }}" alt="{{ $image->title }}" class="slider-thumb">
                <div class="slider-info">
                    <p><strong>{{ $image->title }}</strong></p>
                    <p>Orden: <span class="order-number">{{ $image->order }}</span></p>
                    <p>Estado: 
                        <span 
                            id="status-label-{{ $image->id }}" 
                            class="status-label {{ $image->status ? 'text-success' : 'text-danger' }}"
                        >
                            {{ $image->status ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                    <div class="slider-actions">
                        <button onclick="openModalEditSlider({{ $image->id }})">Editar</button>
                        <button onclick="deleteSlider({{ $image->id }})">Eliminar</button>
                        <button 
                            id="status-btn-{{ $image->id }}" 
                            onclick="toggleStatus({{ $image->id }})"
                            class="{{ $image->status ? 'btn-active' : 'btn-inactive' }}"
                        >
                            {{ $image->status ? 'Desactivar' : 'Activar' }}
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<a href="#" class="btn-floating" onclick="openModalSlider()">+</a>
@endsection
@include('components.administration.slider-image.modal-create-slider-image')
@include('components.administration.slider-image.modal-update-slider-image')
<!-- Estilos -->
<style>
.slider-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.slider-item {
    width: 200px;
    border: 1px solid #ccc;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    cursor: grab;
    cursor: move; /* Cambia cursor a "mover" */
    user-select: none;
    opacity: 1;
    transition: transform 0.2s, opacity 0.2s;
}
.slider-item.dragging {
    opacity: 0.5;
    transform: scale(1.02);
}
.slider-thumb {
    width: 100%;
    height: 120px;
    object-fit: cover;
}
.slider-info {
    padding: 10px;
}
.slider-actions {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-top: 10px;
}
.slider-actions button {
    padding: 5px;
    border: none;
    border-radius: 4px;
    background-color: #0056b3;
    color: white;
    cursor: pointer;
}
.slider-actions button:hover {
    background-color: #003d80;
}

.btn-active {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-inactive {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
}

#slider-list {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 20px;
    background-color: #f9f9f9;
}
</style>