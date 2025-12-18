{{-- Componente para mostrar mensajes con SweetAlert2 --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#dc3545'
        });
    });
</script>
@endif

@if(session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: '¡Advertencia!',
            text: '{{ session('warning') }}',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#ffc107'
        });
    });
</script>
@endif

@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            title: 'Información',
            text: '{{ session('info') }}',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#17a2b8'
        });
    });
</script>
@endif

