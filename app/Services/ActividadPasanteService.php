<?php

namespace App\Services;

use App\Models\ActividadPasante;
use App\Repositories\ActividadPasanteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ActividadPasanteService
{
    protected ActividadPasanteRepository $repository;

    public function __construct(ActividadPasanteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): ActividadPasante
    {
        return DB::transaction(function () use ($data) {
            // Manejar subida de archivos
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            if (isset($data['evidencia_documento']) && $data['evidencia_documento']) {
                $data['evidencia_documento'] = $this->guardarEvidencia($data['evidencia_documento'], 'documentos');
            }

            $data['user_id'] = auth()->id();
            $actividad = $this->repository->create($data);

            Log::info('Actividad de pasante creada', [
                'actividad_id' => $actividad->id_actividad,
                'user_id' => $actividad->user_id
            ]);

            return $actividad;
        });
    }

    public function update(ActividadPasante $actividad, array $data): ActividadPasante
    {
        return DB::transaction(function () use ($actividad, $data) {
            // Manejar actualización de archivos
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                if ($actividad->evidencia_foto) {
                    Storage::disk('public')->delete($actividad->evidencia_foto);
                }
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            if (isset($data['evidencia_documento']) && $data['evidencia_documento']) {
                if ($actividad->evidencia_documento) {
                    Storage::disk('public')->delete($actividad->evidencia_documento);
                }
                $data['evidencia_documento'] = $this->guardarEvidencia($data['evidencia_documento'], 'documentos');
            }

            $this->repository->update($actividad, $data);
            $actividad->refresh();

            Log::info('Actividad de pasante actualizada', [
                'actividad_id' => $actividad->id_actividad
            ]);

            return $actividad;
        });
    }

    public function aprobar(ActividadPasante $actividad): ActividadPasante
    {
        return DB::transaction(function () use ($actividad) {
            $this->repository->update($actividad, [
                'aprobada' => true,
                'aprobada_por' => auth()->id(),
                'fecha_aprobacion' => now()
            ]);

            $actividad->refresh();

            Log::info('Actividad de pasante aprobada', [
                'actividad_id' => $actividad->id_actividad,
                'aprobada_por' => auth()->id()
            ]);

            return $actividad;
        });
    }

    public function delete(ActividadPasante $actividad): bool
    {
        return DB::transaction(function () use ($actividad) {
            // Eliminar archivos asociados
            if ($actividad->evidencia_foto) {
                Storage::disk('public')->delete($actividad->evidencia_foto);
            }
            if ($actividad->evidencia_documento) {
                Storage::disk('public')->delete($actividad->evidencia_documento);
            }

            return $this->repository->delete($actividad);
        });
    }

    protected function guardarEvidencia($file, string $tipo): string
    {
        $nombreArchivo = time() . '_' . $file->getClientOriginalName();
        $ruta = "pasantes/{$tipo}/" . $nombreArchivo;
        $file->storeAs("pasantes/{$tipo}", $nombreArchivo, 'public');
        return $ruta;
    }

    public function getPaginated(array $filters = [], int $perPage = 10)
    {
        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    public function findById(string $id): ?ActividadPasante
    {
        return $this->repository->findById($id);
    }

    public function getEstadisticas(int $userId = null): array
    {
        return $this->repository->getEstadisticas($userId);
    }
}

