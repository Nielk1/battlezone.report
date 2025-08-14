{{-- filepath: resources/views/apidoc/index.blade.php --}}
{{--@extends(request()->query('ajax') == 2 ? 'layouts.ajax-subcontent' : 'layouts.channels')--}}
@php
    $ajax = request()->query('ajax');
    $layouts = [
        3 => 'layouts.ajax-subcontent', // there are no children links so level doesn't really matter
        //1 => 'layouts.channels', // Full page content, children links need level 3
    ];
    $layout = $layouts[$ajax] ?? 'layouts.channels';
    $ajaxnav = 3;
@endphp
@extends($layout, ['ajaxnav' => $ajaxnav])

@section('title', 'Battlezone Field Report - API Reference')

@section('sub-content')
<div class="page-container">
    <div class="sidebar3">
        <div class="header-bar header-bar-menu-pad2">
            <div class="flex-grow-1 text-truncate">{{ $doc_name }}</div>
            {{--<div>NO DATE</div>--}}
        </div>
        <div id="main-scrollable-content" class="sidebar3-content">
            <div class="container">
                @foreach($content as $item)
                    <span class="print-and-select">===================================================</span>
                    <div class="content-item" data-spy="section" id="{{ $item['code'] }}">
                        <h2>
                            {{ $item['name'] }}
                            <?php
                                $typeBase = $item['name'] ?? null;
                                if (isset($typeBase) && isset($type_id_map[$typeBase])) {
                                    echo(' : <a href="#' . $type_id_map[$typeBase] . '">' . e($typeBase) . '</a>');
                                }
                            ?>
                        </h2>

                        @if(isset($item['authors']) && count($item['authors']) > 0)
                            <div class="documentation-authors">
                                <strong>Authors:</strong>
                                <ul>
                                    @foreach($item['authors'] as $author)
                                        <li>{{ $author }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <span class="print-and-select">----</span>
                        <div class="documentation-desc">{!! $item['desc'] !!}</div>
                        @if(isset($item['children']) && count($item['children']) > 0)
                            <span class="print-and-select">>></span>
                            <div>
                                @foreach($item['children'] as $child)
                                    @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map])
                                @endforeach
                            </div>
                            <span class="print-and-select"><<</span>
                        @endif
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
