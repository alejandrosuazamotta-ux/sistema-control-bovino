<?php

namespace App\Services;

use App\Models\ApoyoReproductivoPasante;
use App\Repositories\ApoyoReproductivoPasanteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApoyoReproductivoPasanteService
{
    protected ApoyoReproductivoPasanteRepository $repository;

    public function __construct(ApoyoReproductivoPasanteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): ApoyoReproductivoPasante
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            if (isset($data['evidencia_documento']) && $data['evidencia_documento']) {
                $data['evidencia_documento'] = $this->guardarEvidencia($data['evidencia_documento'], 'documentos');
            }

            $data['user_id'] = auth()->id();
            $apoyo = $this->repository->create($data);

            Log::info('Apoyo reproductivo de pasante creado', [
                'apoyo_id' => $apoyo->id_apoyo_reproductivo,
                'user_id' => $apoyo->user_id
            ]);

            return $apoyo;
        });
    }

    public function update(ApoyoReproductivoPasante $apoyo, array $data): ApoyoReproductivoPasante
    {
        return DB::transaction(function () use ($apoyo, $data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                if ($apoyo->evidencia_foto) {
                    Storage::disk('public')->delete($apoyo->evidencia_foto);
                }
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            if (isset($data['evidencia_documento']) && $data['evidencia_documento']) {
                if ($apoyo->evidencia_documento) {
                    Storage::disk('public')->delete($apoyo->evidencia_documento);
                }
                $data['evidencia_documento'] = $this->guardarEvidencia($data['evidencia_documento'], 'documentos');
            }

            $this->repository->update($apoyo, $data);
            $apoyo->refresh();

            Log::info('Apoyo reproductivo de pasante actualizado', [
                'apoyo_id' => $apoyo->id_apoyo_reproductivo
            ]);

            return $apoyo;
        });
    }

    public function aprobar(ApoyoReproductivoPasante $apoyo): ApoyoReproductivoPasante
    {
        return DB::transaction(function () use ($apoyo) {
            $this->repository->update($apoyo, [
                'aprobada' => true,
                'aprobada_por' => auth()->id(),
                'fecha_aprobacion' => now()
            ]);

            $apoyo->refresh();

            Log::info('Apoyo reproductivo de pasante aprobado', [
                'apoyo_id' => $apoyo->id_apoyo_reproductivo,
                'aprobada_por' => auth()->id()
            ]);

            return $apoyo;
        });
    }

    public function delete(ApoyoReproductivoPasante $apoyo): bool
    {
        return DB::transaction(function () use ($apoyo) {
            if ($apoyo->evidencia_foto) {
                Storage::disk('public')->delete($apoyo->evidencia_foto);
            }
            if ($apoyo->evidencia_documento) {
                Storage::disk('public')->delete($apoyo->evidencia_documento);
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

    public function findById(string $id): ?ApoyoReproductivoPasante
    {
        return $this->repository->findById($id);
    }

    public function getEstadisticas(int $userId = null): array
    {
        return $this->repository->getEstadisticas($userId);
    }
}

