@props(['items' => [], 'title' => null])
<nav class="flex items-center text-xs text-gray-500 mb-2" aria-label="Breadcrumb">
    @if($title)
        <span class="font-semibold text-gray-700 mr-2">{{ $title }}</span>
    @endif
    @foreach($items as $i => $item)
        @if($i > 0)
            <span class="mx-1">/</span>
        @endif
        @if(isset($item['url']))
            <a href="{{ $item['url'] }}" class="hover:underline text-blue-600">{{ $item['label'] }}</a>
        @else
            <span class="text-gray-700">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
