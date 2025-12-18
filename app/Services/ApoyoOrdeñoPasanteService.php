<?php

namespace App\Services;

use App\Models\ApoyoOrdeñoPasante;
use App\Repositories\ApoyoOrdeñoPasanteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApoyoOrdeñoPasanteService
{
    protected ApoyoOrdeñoPasanteRepository $repository;

    public function __construct(ApoyoOrdeñoPasanteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): ApoyoOrdeñoPasante
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            $data['user_id'] = auth()->id();
            $apoyo = $this->repository->create($data);

            Log::info('Apoyo de ordeño de pasante creado', [
                'apoyo_id' => $apoyo->id_apoyo_ordeno,
                'user_id' => $apoyo->user_id
            ]);

            return $apoyo;
        });
    }

    public function update(ApoyoOrdeñoPasante $apoyo, array $data): ApoyoOrdeñoPasante
    {
        return DB::transaction(function () use ($apoyo, $data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                if ($apoyo->evidencia_foto) {
                    Storage::disk('public')->delete($apoyo->evidencia_foto);
                }
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            $this->repository->update($apoyo, $data);
            $apoyo->refresh();

            Log::info('Apoyo de ordeño de pasante actualizado', [
                'apoyo_id' => $apoyo->id_apoyo_ordeno
            ]);

            return $apoyo;
        });
    }

    public function aprobar(ApoyoOrdeñoPasante $apoyo): ApoyoOrdeñoPasante
    {
        return DB::transaction(function () use ($apoyo) {
            $this->repository->update($apoyo, [
                'aprobada' => true,
                'aprobada_por' => auth()->id(),
                'fecha_aprobacion' => now()
            ]);

            $apoyo->refresh();

            Log::info('Apoyo de ordeño de pasante aprobado', [
                'apoyo_id' => $apoyo->id_apoyo_ordeno,
                'aprobada_por' => auth()->id()
            ]);

            return $apoyo;
        });
    }

    public function delete(ApoyoOrdeñoPasante $apoyo): bool
    {
        return DB::transaction(function () use ($apoyo) {
            if ($apoyo->evidencia_foto) {
                Storage::disk('public')->delete($apoyo->evidencia_foto);
            }

            return $this->repository->delete($apoyo);
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

    public function findById(string $id): ?ApoyoOrdeñoPasante
    {
        return $this->repository->findById($id);
    }

    public function getEstadisticas(int $userId = null): array
    {
        return $this->repository->getEstadisticas($userId);
    }
}

