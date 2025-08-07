{{-- filepath: resources/views/apidoc/index.blade.php --}}
@extends(request()->query('ajax') == 2 ? 'layouts.ajax-subcontent' : 'layouts.channels')

@section('title', 'Battlezone Field Report - API Reference')

@section('sub-content')
<div class="page-container">
    <div class="sidebar3">
        <div class="header-bar header-bar-menu-pad2">
            <div class="flex-grow-1 text-truncate">NO TITLE</div>
            <div>NO DATE</div>
        </div>
        <div id="main-scrollable-content" class="sidebar3-content">
            <div class="container">
                @foreach($content as $item)
                    <div class="content-item" data-spy="section" id="{{ $item['code'] }}">
                        <h2>{{ $item['name'] }}</h2>
                        <div class="documentation-desc">{!! $item['desc'] !!}</div>
                        @if(isset($item['children']) && count($item['children']) > 0)
                            <div>
                                @foreach($item['children'] as $child)
                                    @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map])
                                    {{--<div class="ps-3 border-5 border-start border-primary" data-spy="section" id="{{ $child['code'] }}">
                                        <div class="d-flex align-items-center flex-row gap-2 mb-2">
                                            @if(isset($child['glyph']))
                                                @if (str_starts_with($child['glyph'], 'bi '))
                                                    <span class="font-icon"><i class="{{ $child['glyph'] }}"></i></span>
                                                @else
                                                    <span class="svg-icon">{!! File::get(resource_path('svg/' . $child['glyph'] . '.svg')) !!}</span>
                                                @endif
                                            @endif
                                            <h3 class="mb-0">{{ $child['name'] }}</h3>
                                        </div>
                                        <div class="documentation-desc">{!! $child['desc'] !!}</div>
                                        @if(isset($child['children']) && count($child['children']) > 0)
                                            <div>
                                                @foreach($child['children'] as $subchild)
                                                    @include('partials.api-field', ['content' => $subchild])
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>--}}
                                    {{-- @foreach($item['children'] as $child) --}}
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
