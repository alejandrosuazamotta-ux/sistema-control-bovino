<?php

namespace App\Constants;

/**
 * Constantes centralizadas para mensajes de excepción
 * 
 * Evita inconsistencias y facilita mantenimiento
 */
class ExceptionMessages
{
    // ============================================
    // PRODUCCIÓN LECHERA
    // ============================================
    
    public const PRODUCCION_VACA_NO_EXISTE = 'La vaca seleccionada no existe.';
    public const PRODUCCION_VACA_EN_RETIRO_ORDENO = 'La vaca está en período de retiro de ordeño. No se puede registrar producción.';
    public const PRODUCCION_VACA_EN_RETIRO_PRODUCCION = 'La vaca está en período de retiro de producción. No se puede registrar producción.';
    public const PRODUCCION_RESTRICCION_ORDENO = 'La vaca tiene una restricción de ordeño activa (prueba sanitaria positiva). No se puede registrar producción.';
    public const PRODUCCION_VACA_INHABILITADA = 'La vaca está inhabilitada por prueba sanitaria positiva (Brucelosis/Tuberculosis). No se puede registrar producción.';
    public const PRODUCCION_DUPLICADA = 'Ya existe un registro de producción para esta vaca en la fecha y turno seleccionados.';
    public const PRODUCCION_VACA_NO_LACTANCIA = 'Solo se puede registrar producción para vacas en estado de Lactancia. La vaca seleccionada está en estado: %s';
    public const PRODUCCION_ACTUALIZAR_RESTRICCION_ORDENO = 'La vaca tiene una restricción de ordeño activa (prueba sanitaria positiva). No se puede actualizar producción.';
    public const PRODUCCION_ACTUALIZAR_VACA_INHABILITADA = 'La vaca está inhabilitada por prueba sanitaria positiva (Brucelosis/Tuberculosis). No se puede actualizar producción.';
    public const PRODUCCION_ACTUALIZAR_DUPLICADA = 'Ya existe otro registro de producción para esta vaca en la fecha y turno seleccionados.';

    // ============================================
    // RETIROS
    // ============================================
    
    public const RETIRO_VACA_NO_EXISTE = 'La vaca seleccionada no existe.';
    public const RETIRO_SOLAPADO = 'Ya existe un retiro activo de %s para esta vaca en el período seleccionado.';
    public const RETIRO_FECHAS_INCONSISTENTES = 'La fecha de inicio no puede ser posterior a la fecha de fin.';
    public const RETIRO_NO_EXISTE = 'El retiro seleccionado no existe.';
    public const RETIRO_NO_PUEDE_ELIMINAR = 'No se puede eliminar el retiro seleccionado.';

    // ============================================
    // PRUEBAS SANITARIAS / SALUD
    // ============================================
    
    public const SALUD_VACA_NO_EXISTE = 'La vaca seleccionada no existe.';
    public const PRUEBA_SANITARIA_VACA_NO_EXISTE = 'La vaca seleccionada no existe.';
    public const PRUEBA_SANITARIA_CERRADA = 'No se puede editar una prueba sanitaria cerrada.';
    public const PRUEBA_SANITARIA_NO_EXISTE = 'La prueba sanitaria seleccionada no existe.';

    // ============================================
    // MEDICAMENTOS
    // ============================================
    
    public const MEDICAMENTO_NO_EXISTE = 'El medicamento seleccionado no existe.';
    public const MEDICAMENTO_INACTIVO = 'El medicamento seleccionado no está activo.';
    public const USO_MEDICAMENTO_VACA_NO_EXISTE = 'La vaca seleccionada no existe.';
    public const USO_MEDICAMENTO_FECHA_FUTURA = 'La fecha de aplicación no puede ser futura.';

    // ============================================
    // VACAS
    // ============================================
    
    public const VACA_NO_EXISTE = 'La vaca seleccionada no existe.';
    public const VACA_CODIGO_DUPLICADO = 'Ya existe una vaca con el código %s.';

    // ============================================
    // CRÍAS
    // ============================================
    
    public const CRIA_NO_EXISTE = 'La cría seleccionada no existe.';
    public const CRIA_VACA_MADRE_NO_EXISTE = 'La vaca madre seleccionada no existe.';

    // ============================================
    // INVENTARIO
    // ============================================
    
    public const INVENTARIO_PRODUCTO_NO_EXISTE = 'El producto seleccionado no existe.';
    public const INVENTARIO_STOCK_INSUFICIENTE = 'Stock insuficiente. Stock actual: %s, Stock requerido: %s';
    public const INVENTARIO_STOCK_NEGATIVO = 'No se puede realizar una salida que resulte en stock negativo.';

    // ============================================
    // VALIDACIONES GENERALES
    // ============================================
    
    public const FECHA_FUTURA = 'La fecha no puede ser futura.';
    public const FECHA_INVALIDA = 'La fecha proporcionada no es válida.';
    public const DATO_REQUERIDO = 'El campo %s es obligatorio.';
    public const VALOR_INVALIDO = 'El valor proporcionado para %s no es válido.';
}

