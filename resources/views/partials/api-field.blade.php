<div class="apidoc ps-2 my-3 border-5 border-start border-primary" data-spy="section" id="{{ $content['code'] }}">
    @php($nillable = false)
    @php($has_primitive = false)
    @php($has_non_primitive = false)
    @php($type_code = null)
    @foreach($content['type'] as $type)
        @switch($type)
            @case('section')
                @php($type_code = 'section')
                @break
            @case('type')
                @php($type_code = 'type')
                @php($has_non_primitive = true)
                @break
            @case('alias')
                @php($type_code = 'alias')
                @php($has_non_primitive = true)
                @break
            @case('function')
            @case('function_overload')
                @php($type_code = 'function')
                @php($has_non_primitive = true)
                @break
            @case('string')
            @case('integer')
            @case('number')
            @case('boolean')
                @php($has_primitive = true)
                @break
        @endswitch
        @if($type == 'nil')
            @php($nillable = true)
        @endif
    @endforeach
    <span class="print-and-select">---------------------------------------------------</span>
    <div class="{{--api-sticky-block--}} d-flex flex-row align-items-stretch gap-1 mb-1">
        @if(isset($content['glyph']))
            <div class="bg-primary d-flex align-items-center">
                <div class="apidoc-icon">
                @if (str_starts_with($content['glyph'], 'bi '))
                    <span class="font-icon"><i class="{{ $content['glyph'] }}"></i></span>
                @else
                    {{--<span class="svg-icon">{!! File::get(resource_path('svg/' . $content['glyph'] . '.svg')) !!}</span>--}}
                    <span class="svg-icon"><svg width="24" height="24"><use xlink:href="#svg/{{ $content['glyph'] }}"></use></svg></span>
                @endif
                </div>
            </div>
        @endif
        <div class="d-flex justify-content-between flex-column">
            <div>
                <span class="h5">
                    {{ $content['name'] }}
                    @if(isset($content['base']))
                        @if(($type_code ?? null) == 'type' || ($type_code ?? null) == 'alias')
                            :
                            @if(isset($type_id_map[$content['base']]))
                                <a href="#{{ $type_id_map[$content['base']] }}">{{ $content['base'] }}</a>
                            @else
                                {{ $content['base'] }}
                            @endif
                        @endif
                    @endif
                </span>
                @if($type_code == 'function')
                    <span class="h4">(</span>
                    <span style="vertical-align: text-bottom;">
                    <?php
                        if(isset($content['args'])) {
                            $argsOut = [];
                            foreach($content['args'] as $arg) {
                                $typesOut = [];
                                if(isset($arg['type'])) {
                                    foreach($arg['type'] as $type) {
                                        if(isset($type_id_map[rtrim($type, '?')])) {
                                            $typesOut[] = '<a href="#' . e($type_id_map[rtrim($type, '?')]) . '">' . e($type) . '</a>';
                                        } else {
                                            $typesOut[] = e($type);
                                        }
                                    }
                                }
                                $argsOut[] = '<span class="apidoc-arg"><span class="fw-bolder text-info">' . e($arg['name'] ?? '???') . '</span>: ' . implode('<span class="fw-bold">|</span>', $typesOut) . '</span>';
                            }
                            echo implode('<span class="h5">,</span> ', $argsOut);
                        }
                    ?>
                    </span>
                    <span class="h4">)</span>
                    @if(isset($content['returns']) && count($content['returns']) > 0)
                    <span class="align-text-middle"><i class="bi bi-arrow-right"><span class="select-only">-&gt;</span></i></span>
                    <span class="align-text-bottom">
                    <?php
                        $argsOut = [];
                        $return_index = 1;
                        foreach($content['returns'] as $return) {
                            $typesOut = [];
                            if(isset($return['type'])) {
                                foreach($return['type'] as $type) {
                                    if(isset($type_id_map[rtrim($type, '?')])) {
                                        $typesOut[] = '<a href="#' . e($type_id_map[rtrim($type, '?')]) . '">' . e($type) . '</a>';
                                    } else {
                                        $typesOut[] = e($type);
                                    }
                                }
                            }
                            //$argsOut[] = '<span class="fw-bolder text-danger">' . e($return['name'] ?? '???') . '</span>: ' . implode('<span class="h5">|</span>', $typesOut);
                            $argsOut[] = '<span class="apidoc-arg"><span class="fw-bolder text-danger">' . e($return['name'] ?? '['.$return_index.']') . '</span>: ' . implode('<span class="fw-bold">|</span>', $typesOut) . '</span>';
                            $return_index++;
                        }
                        echo implode('<span class="h5">,</span> ', $argsOut);
                    ?>
                    </span>
                    @endif
                @endif
            </div>
            <div class="d-flex flex-row gap-1">
                @foreach($content['type'] as $type)
                    @if($type === 'nil')
                        @continue
                    @endif
                    <span class="badge text-bg-primary text-wrap"><span class="print-and-select">[</span>{{ $type }}<span class="print-and-select">]</span></span>
                    {{--<span class="badge text-bg-secondary">{{ $type }}</span>
                    <span class="badge text-bg-success">{{ $type }}</span>
                    <span class="badge text-bg-danger">{{ $type }}</span>
                    <span class="badge text-bg-warning">{{ $type }}</span>
                    <span class="badge text-bg-info">{{ $type }}</span>
                    <span class="badge text-bg-light">{{ $type }}</span>
                    <span class="badge text-bg-dark">{{ $type }}</span>--}}
                @endforeach
                @if($nillable)
                    <span class="badge text-bg-secondary"><span class="print-and-select">[</span>nil<span class="print-and-select">]</span></span>
                @endif
                @if(isset($content['global']) && $content['global'])
                    <span class="badge text-bg-success"><span class="print-and-select">[</span>global<span class="print-and-select">]</span></span>
                @endif
                @if(isset($content['deprecated']) && $content['deprecated'])
                    <span class="badge text-bg-danger"><span class="print-and-select">[</span>deprecated<span class="print-and-select">]</span></span>
                @endif
                @if(isset($content['tags']['internal_use']) && $content['tags']['internal_use'])
                    <span class="badge text-bg-warning"><span class="print-and-select">[</span>internal use<span class="print-and-select">]</span></span>
                @endif
                @if(isset($content['tags']['version']))
                    <span class="badge text-bg-info"><span class="print-and-select">[</span>version {{ $content['tags']['version'] }}<span class="print-and-select">]</span></span>
                @endif
                @if(isset($content['tags']))
                    @foreach($content['tags'] as $tag => $tagcontent)
                        @if($tag === 'version' || $tag === 'internal_use' || $tag === '(i)' || $tag === '(!)' || $tag === '(!!)' || $tag === 'mod')
                            @continue
                        @endif
                        <span class="badge text-bg-info"><span class="print-and-select">[</span>{{ $tag }}: {{ $tagcontent }}<span class="print-and-select">]</span></span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @if(!($has_primitive && !$has_non_primitive))
        @switch($type_code ?? null)
            @case('type')
            @case('alias')
            @case('section')
                @break
            @case ('function')
                @if(isset($content['args']))
                @foreach($content['args'] as $arg)
                    <span class="print-and-select">----</span>
                    <div class="arg-item param mb-1 border-15 border-start border-info">
                        <span class="print-and-select">@param&nbsp;</span><span class="fw-bolder text-info">{{ $arg['name'] ?? '' }}</span>
                        <?php
                            $typesOut = [];
                            if(isset($arg['type'])) {
                                foreach($arg['type'] as $type) {
                                    if(isset($type_id_map[rtrim($type, '?')])) {
                                        $typesOut[] = '<a href="#' . e($type_id_map[rtrim($type, '?')]) . '">' . e($type) . '</a>';
                                    } else {
                                        $typesOut[] = e($type);
                                    }
                                }
                            }
                            echo implode('<span class="fw-bold">|</span>', $typesOut);
                        ?>
                        @if(isset($arg['tags']))
                            <div class="d-flex flex-row gap-1">
                                @if(isset($arg['tags']['version']))
                                    <span class="badge text-bg-info"><span class="print-and-select">[</span>version {{ $arg['tags']['version'] }}<span class="print-and-select">]</span></span>
                                @endif
                                @foreach($arg['tags'] as $tag => $tagcontent)
                                    @if($tag === 'version')
                                        @continue
                                    @endif
                                    <span class="badge text-bg-info"><span class="print-and-select">[</span>{{ $tag }}: {{ $tagcontent }}<span class="print-and-select">]</span></span>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($arg['desc']))
                            {!! $arg['desc'] !!}
                        @endif
                    </div>
                @endforeach
                @endif
                @if(isset($content['returns']))
                @php($return_index = 1)
                @foreach($content['returns'] as $return)
                    <span class="print-and-select">----</span>
                    <div class="arg-item return mb-1 border-15 border-start border-danger position-relative">
                        <span class="print-and-select">@return&nbsp;</span><span class="fw-bolder text-danger">{{ $return['name'] ?? '['.$return_index.']' }}</span>
                        <?php
                            $typesOut = [];
                            if(isset($return['type'])) {
                                foreach($return['type'] as $type) {
                                    if(isset($type_id_map[rtrim($type, '?')])) {
                                        $typesOut[] = '<a href="#' . e($type_id_map[rtrim($type, '?')]) . '">' . e($type) . '</a>';
                                    } else {
                                        $typesOut[] = e($type);
                                    }
                                }
                            }
                            echo implode('<span class="fw-bold">|</span>', $typesOut);
                            $return_index++;
                        ?>
                        @if(isset($return['tags']))
                            <div class="d-flex flex-row gap-1">
                                @if(isset($return['tags']['version']))
                                    <span class="badge text-bg-info"><span class="print-and-select">[</span>version {{ $return['tags']['version'] }}<span class="print-and-select">]</span></span>
                                @endif
                                @foreach($return['tags'] as $tag => $tagcontent)
                                    @if($tag === 'version')
                                        @continue
                                    @endif
                                    <span class="badge text-bg-info"><span class="print-and-select">[</span>{{ $tag }}: {{ $tagcontent }}<span class="print-and-select">]</span></span>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($return['desc']))
                            {!! $return['desc'] !!}
                        @endif
                    </div>
                @endforeach
                @endif
                @break
            @default
                @if(isset($type_code))
                <h6>TYPECODE: {{ $type_code }}</h6>
                @endif
                @if(isset($content['type']))
                @foreach($content['type'] as $type)
                    <h6>TYPE: {{ $type }}</h6>
                @endforeach
                @endif
                <h6>SPECIAL: {{ $content['special'] ?? 'NO SPECIAL' }}</h6>
                <h6>GLYPH: {{ $content['glyph'] ?? 'NO GLYPH' }}</h6>
                @if(isset($content['view']))
                    <pre>VIEW: {{ $content['view'] }}</pre>
                @endif
        @endswitch
    @endif
    @if(isset($content['tags']['(!!)']))
    @foreach($content['tags']['(!!)'] as $tag)
        <span class="print-and-select">----</span>
        <div class="alert alert-danger d-flex align-items-center my-1" role="alert">
            <i class="bi bi-exclamation-octagon-fill alert-icon" aria-label="Error:"></i>
            <div>
                <span class="print-and-select">Error:</span>
                @if(isset($tag['name']) && !empty($tag['name']))
                <strong>{{ $tag['name'] }}:</strong>
                @endif
                {{ $tag['desc'] }}
            </div>
        </div>
    @endforeach
    @endif
    @if(isset($content['tags']['(!)']))
    @foreach($content['tags']['(!)'] as $tag)
        <span class="print-and-select">----</span>
        <div class="alert alert-warning d-flex align-items-center my-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-icon" aria-label="Warning:"></i>
            <div>
                <span class="print-and-select">Warning:</span>
                @if(isset($tag['name']) && !empty($tag['name']))
                <strong>{{ $tag['name'] }}:</strong>
                @endif
                {{ $tag['desc'] }}
            </div>
        </div>
    @endforeach
    @endif
    @if(isset($content['tags']['(i)']))
    @foreach($content['tags']['(i)'] as $tag)
        <span class="print-and-select">----</span>
        <div class="alert alert-info d-flex align-items-center my-1" role="alert">
            <i class="bi bi-info-circle-fill alert-icon" aria-label="Info:"></i>
            <div>
                <span class="print-and-select">Info:</span>
                @if(isset($tag['name']) && !empty($tag['name']))
                <strong>{{ $tag['name'] }}:</strong>
                @endif
                {{ $tag['desc'] }}
            </div>
        </div>
    @endforeach
    @endif
    @if(isset($content['tags']['mod']))
    @foreach($content['tags']['mod'] as $tag)
        <span class="print-and-select">----</span>
        <div class="alert alert-light d-flex align-items-center my-1" role="alert">
            <i class="bi bi-tools alert-icon" aria-label="Mod:"></i>
            <div>
                <span class="print-and-select">Mod:</span>
                @if(isset($tag['name']) && !empty($tag['name']))
                <strong>{{ $tag['name'] }}:</strong>
                @endif
                {{ $tag['desc'] }}
            </div>
        </div>
    @endforeach
    @endif
    <span class="print-and-select">----</span>
    <div class="documentation-desc">{!! $content['desc'] !!}</div>
    @if(isset($content['children']) && count($content['children']) > 0)
        <span class="print-and-select">>></span>
        <div>
            @foreach($content['children'] as $child)
                @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map])
            @endforeach
        </div>
        <span class="print-and-select"><<</span>
    @endif
</div>
