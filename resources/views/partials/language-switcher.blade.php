<div class="relative">
    <select
        onchange="window.location.href = window.location.pathname + '?lang=' + this.value"
        class="bg-white/10 text-white text-xs rounded-full pl-2 pr-6 py-1.5 border-0 appearance-none cursor-pointer"
        aria-label="{{ __('peta.cara_ke_sini') }}"
    >
        @foreach (config('languages.supported') as $code => $bahasa)
            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }} class="text-gray-900">
                {{ $bahasa['flag'] }} {{ $bahasa['label'] }}
            </option>
        @endforeach
    </select>
</div>
