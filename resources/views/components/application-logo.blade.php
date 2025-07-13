@props(['src' => asset('images/my-logo.png'), 'alt' => 'App Logo'])
<img src="{{ $src }}" alt="{{ $alt }}"
     class="h-[120px] sm:h-[140px] md:h-[160px] lg:h-[170px] xl:h-[180px] w-auto mx-auto"
     {{ $attributes }}>