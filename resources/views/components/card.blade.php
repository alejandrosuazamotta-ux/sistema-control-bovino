@props(['title' => null, 'icon' => null, 'class' => '', 'headerClass' => '', 'bodyClass' => '', 'footer' => null, 'hover' => false])

<div class="bg-white rounded-card shadow-saas {{ $hover ? 'card-hover' : '' }} {{ $class }} animate-fade-in-up">
    @if($title || $icon)
        <div class="px-6 py-4 border-b border-gray-100 {{ $headerClass }}">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="flex-shrink-0 w-10 h-10 bg-spg-primary/10 rounded-lg flex items-center justify-center icon-rotate">
                        <i class="{{ $icon }} text-spg-primary text-lg"></i>
                    </div>
                @endif
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-900 font-display">{{ $title }}</h3>
                @endif
            </div>
        </div>
    @endif
    <div class="p-4 md:p-6 {{ $bodyClass }}">
        {{ $slot }}
    </div>
    @if($footer)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-card">
            {{ $footer }}
        </div>
    @endif
</div>

