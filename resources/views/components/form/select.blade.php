@props(['disabled' => false, 'icon' => null, 'label' => null, 'error' => null])

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <i class="fas {{ $icon }} text-spg-deepblue/60"></i>
            </div>
        @endif
        <select 
            {{ $disabled ? 'disabled' : '' }} 
            {!! $attributes->merge([
                'class' => 'input-focus border-gray-300 focus:border-spg-primary focus:ring-spg-primary rounded-lg shadow-sm block w-full appearance-none bg-white ' . 
                          ($icon ? 'pl-10' : '') . 
                          ($error ? 'border-red-500 focus:ring-red-500' : '')
            ]) !!}
        >
            {{ $slot }}
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <i class="fas fa-chevron-down text-gray-400"></i>
        </div>
    </div>
    
    @if($error)
        <p class="text-sm text-red-600 animate-fade-in">{{ $error }}</p>
    @endif
</div>

