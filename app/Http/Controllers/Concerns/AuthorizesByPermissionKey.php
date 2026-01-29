<?php
namespace App\Http\Controllers\Concerns;

trait AuthorizesByPermissionKey
{
    protected function authorizeCrud(string $key): void
    {
        // Lectura
        $this->middleware("perm:{$key}.view")->only(['index','show']);
        // Crear
        $this->middleware("perm:{$key}.create")->only(['create','store']);
        // Editar
        $this->middleware("perm:{$key}.update")->only(['edit','update']);
        // Eliminar
        $this->middleware("perm:{$key}.delete")->only(['destroy']);
    }
}