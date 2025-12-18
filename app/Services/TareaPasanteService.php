<?php

namespace App\Services;

use App\Models\TareaPasante;
use App\Repositories\TareaPasanteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TareaPasanteService
{
    protected TareaPasanteRepository $repository;

    public function __construct(TareaPasanteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): TareaPasante
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            if (isset($data['evidencia_documento']) && $data['evidencia_documento']) {
                $data['evidencia_documento'] = $this->guardarEvidencia($data['evidencia_documento'], 'documentos');
            }

            $data['asignada_por'] = auth()->id();
            $tarea = $this->repository->create($data);

            Log::info('Tarea de pasante creada', [
                'tarea_id' => $tarea->id_tarea,
                'user_id' => $tarea->user_id
            ]);

            return $tarea;
        });
    }

    public function update(TareaPasante $tarea, array $data): TareaPasante
    {
        return DB::transaction(function () use ($tarea, $data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                if ($tarea->evidencia_foto) {
                    Storage::disk('public')->delete($tarea->evidencia_foto);
                }
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            if (isset($data['evidencia_documento']) && $data['evidencia_documento']) {
                if ($tarea->evidencia_documento) {
                    Storage::disk('public')->delete($tarea->evidencia_documento);
                }
                $data['evidencia_documento'] = $this->guardarEvidencia($data['evidencia_documento'], 'documentos');
            }

            // Si se marca como completada, actualizar fecha
            if (isset($data['estado']) && $data['estado'] === 'Completada' && !$tarea->fecha_completada) {
                $data['fecha_completada'] = now();
            }

            $this->repository->update($tarea, $data);
            $tarea->refresh();

            Log::info('Tarea de pasante actualizada', [
                'tarea_id' => $tarea->id_tarea
            ]);

            return $tarea;
        });
    }

    public function aprobar(TareaPasante $tarea): TareaPasante
    {
        return DB::transaction(function () use ($tarea) {
            $this->repository->update($tarea, [
                'aprobada' => true,
                'aprobada_por' => auth()->id(),
                'fecha_aprobacion' => now()
            ]);

            $tarea->refresh();

            Log::info('Tarea de pasante aprobada', [
                'tarea_id' => $tarea->id_tarea,
                'aprobada_por' => auth()->id()
            ]);

            return $tarea;
        });
    }

    public function delete(TareaPasante $tarea): bool
    {
        return DB::transaction(function () use ($tarea) {
            if ($tarea->evidencia_foto) {
                Storage::disk('public')->delete($tarea->evidencia_foto);
            }
            if ($tarea->evidencia_documento) {
                Storage::disk('public')->delete($tarea->evidencia_documento);
            }

            return $this->repository->delete($tarea);
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

    public function findById(string $id): ?TareaPasante
    {
        return $this->repository->findById($id);
    }

    public function getEstadisticas(int $userId = null): array
    {
        return $this->repository->getEstadisticas($userId);
    }
}

