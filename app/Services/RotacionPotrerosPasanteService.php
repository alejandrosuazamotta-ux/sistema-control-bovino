<?php

namespace App\Services;

use App\Models\RotacionPotrerosPasante;
use App\Repositories\RotacionPotrerosPasanteRepository;
use App\Models\Vaca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RotacionPotrerosPasanteService
{
    protected RotacionPotrerosPasanteRepository $repository;

    public function __construct(RotacionPotrerosPasanteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): RotacionPotrerosPasante
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            $data['user_id'] = auth()->id();
            $rotacion = $this->repository->create($data);

            // Actualizar el potrero de la vaca
            if (isset($data['id_vaca']) && isset($data['id_potrero_destino'])) {
                $vaca = Vaca::find($data['id_vaca']);
                if ($vaca) {
                    $vaca->id_potrero = $data['id_potrero_destino'];
                    $vaca->save();
                }
            }

            Log::info('Rotación de potreros de pasante creada', [
                'rotacion_id' => $rotacion->id_rotacion,
                'user_id' => $rotacion->user_id
            ]);

            return $rotacion;
        });
    }

    public function update(RotacionPotrerosPasante $rotacion, array $data): RotacionPotrerosPasante
    {
        return DB::transaction(function () use ($rotacion, $data) {
            if (isset($data['evidencia_foto']) && $data['evidencia_foto']) {
                if ($rotacion->evidencia_foto) {
                    Storage::disk('public')->delete($rotacion->evidencia_foto);
                }
                $data['evidencia_foto'] = $this->guardarEvidencia($data['evidencia_foto'], 'fotos');
            }

            $this->repository->update($rotacion, $data);
            $rotacion->refresh();

            Log::info('Rotación de potreros de pasante actualizada', [
                'rotacion_id' => $rotacion->id_rotacion
            ]);

            return $rotacion;
        });
    }

    public function aprobar(RotacionPotrerosPasante $rotacion): RotacionPotrerosPasante
    {
        return DB::transaction(function () use ($rotacion) {
            $this->repository->update($rotacion, [
                'aprobada' => true,
                'aprobada_por' => auth()->id(),
                'fecha_aprobacion' => now()
            ]);

            $rotacion->refresh();

            Log::info('Rotación de potreros de pasante aprobada', [
                'rotacion_id' => $rotacion->id_rotacion,
                'aprobada_por' => auth()->id()
            ]);

            return $rotacion;
        });
    }

    public function delete(RotacionPotrerosPasante $rotacion): bool
    {
        return DB::transaction(function () use ($rotacion) {
            if ($rotacion->evidencia_foto) {
                Storage::disk('public')->delete($rotacion->evidencia_foto);
            }

            return $this->repository->delete($rotacion);
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

    public function findById(string $id): ?RotacionPotrerosPasante
    {
        return $this->repository->findById($id);
    }

    public function getEstadisticas(int $userId = null): array
    {
        return $this->repository->getEstadisticas($userId);
    }
}

