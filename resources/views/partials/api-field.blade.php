<div class="ps-3 mb-3 border-5 border-start border-primary" data-spy="section" id="{{ $content['code'] }}">
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
    <div class="apidoc-field-header d-flex align-items-center flex-row gap-2 mb-2">
        @if(isset($content['glyph']))
            @if (str_starts_with($content['glyph'], 'bi '))
                <span class="apidoc font-icon bg-primary"><i class="{{ $content['glyph'] }}"></i></span>
            @else
                <span class="apidoc svg-icon bg-primary">{!! File::get(resource_path('svg/' . $content['glyph'] . '.svg')) !!}</span>
            @endif
        @endif
        <span>
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
                            $argsOut[] = '<span class="fw-bolder text-info">' . e($arg['name'] ?? '???') . '</span>: ' . implode('<span class="fw-bold">|</span>', $typesOut);
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
                        $argsOut[] = '<span class="fw-bolder text-danger">' . e($return['name'] ?? '['.$return_index.']') . '</span>: ' . implode('<span class="fw-bold">|</span>', $typesOut);
                        $return_index++;
                    }
                    echo implode('<span class="h5">,</span> ', $argsOut);
                ?>
                </span>
                @endif
            @endif
        </span>
        @foreach($content['type'] as $type)
            @if($type === 'nil' || $type === 'section' || $type === 'function' || $type === 'function_overload')
                @continue
            @endif
            <span class="badge text-bg-primary">{{ $type }}</span>
            {{--<span class="badge text-bg-secondary">{{ $type }}</span>
            <span class="badge text-bg-success">{{ $type }}</span>
            <span class="badge text-bg-danger">{{ $type }}</span>
            <span class="badge text-bg-warning">{{ $type }}</span>
            <span class="badge text-bg-info">{{ $type }}</span>
            <span class="badge text-bg-light">{{ $type }}</span>
            <span class="badge text-bg-dark">{{ $type }}</span>--}}
        @endforeach
        @if($nillable)
            <span class="badge text-bg-secondary">nil</span>
        @endif
        @if(isset($content['tags']['version']))
            <span class="badge text-bg-info">Version {{ $content['tags']['version'] }}</span>
        @endif
        @if(isset($content['tags']))
            @foreach($content['tags'] as $tag => $tagcontent)
                @if($tag === 'version' || $tag === '(i)' || $tag === '(!)' || $tag === '(!!)' || $tag === 'mod')
                    @continue
                @endif
                <span class="badge text-bg-info">{{ $tag }}: {{ $tagcontent }}</span>
            @endforeach
        @endif
        {{-- untested deprecated --}}
        @if(isset($content['deprecated']) && $content['deprecated'])
            <span class="badge text-bg-danger">deprecated</span>
        @endif
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
                    <div class="arg-item param mb-2 border-15 border-start border-info">
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
                            @foreach($arg['tags'] as $tag => $tagcontent)
                                <span class="badge text-bg-info">{{ $tag }}: {{ $tagcontent }}</span>
                            @endforeach
                        @endif
                        @if(!empty($arg['desc']))
                            {!! $arg['desc'] !!}
                        @endif
                    </div>
                @endforeach
                @endif
                @if(isset($content['returns']))
                @foreach($content['returns'] as $return)
                    @php($return_index = 1)
                    <div class="arg-item return mb-2 border-15 border-start border-danger position-relative">
                        <span class="print-and-select">@param&nbsp;</span><span class="fw-bolder text-danger">{{ $return['name'] ?? '['.$return_index.']' }}</span>
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
                            @foreach($return['tags'] as $tag => $tagcontent)
                                <span class="badge text-bg-info">{{ $tag }}: {{ $tagcontent }}</span>
                            @endforeach
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
                @if(isset($content['args']))
                @foreach($content['args'] as $arg)
                    <div class="arg-item ps-3 mb-1 border-5 border-start border-info">
                        ARG:
                        <strong>{{ $arg['name'] ?? '' }}</strong>
                        @if(isset($arg['type']))
                            @foreach($arg['type'] as $type)
                                <span class="badge text-bg-primary">{{ $type }}</span>
                            @endforeach
                        @endif
                        @if(isset($arg['tags']))
                            @foreach($arg['tags'] as $tag => $tagcontent)
                                <span class="badge text-bg-info">{{ $tag }}: {{ $tagcontent }}</span>
                            @endforeach
                        @endif
                        @if(!empty($arg['desc']))
                            <div>{!! $arg['desc'] !!}</div>
                        @endif
                    </div>
                @endforeach
                @endif
                @if(isset($content['returns']))
                @foreach($content['returns'] as $return)
                    <div class="arg-item ps-3 mb-1 border-5 border-start border-danger">
                        RETURN:
                        <strong>{{ $return['name'] ?? '' }}</strong>
                        @if(isset($return['type']))
                            @foreach($return['type'] as $type)
                                <span class="badge text-bg-primary">{{ $type }}</span>
                            @endforeach
                        @endif
                        @if(isset($return['tags']))
                            @foreach($return['tags'] as $tag => $tagcontent)
                                <span class="badge text-bg-info">{{ $tag }}: {{ $tagcontent }}</span>
                            @endforeach
                        @endif
                        @if(!empty($return['desc']))
                            <div>{!! $return['desc'] !!}</div>
                        @endif
                    </div>
                @endforeach
                @endif
        @endswitch
    @endif
    @if(isset($content['tags']['(!!)']))
    @foreach($content['tags']['(!!)'] as $tag)
        <div class="alert alert-danger d-flex align-items-center my-3" role="alert">
            <i class="bi bi-exclamation-octagon-fill alert-icon" aria-label="Error:"></i>
            <div>
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
        <div class="alert alert-warning d-flex align-items-center my-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-icon" aria-label="Warning:"></i>
            <div>
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
        <div class="alert alert-info d-flex align-items-center my-3" role="alert">
            <i class="bi bi-info-circle-fill alert-icon" aria-label="Info:"></i>
            <div>
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
        <div class="alert alert-light d-flex align-items-center my-3" role="alert">
            <i class="bi bi-tools alert-icon" aria-label="Mod:"></i>
            <div>
                @if(isset($tag['name']) && !empty($tag['name']))
                <strong>{{ $tag['name'] }}:</strong>
                @endif
                {{ $tag['desc'] }}
            </div>
        </div>
    @endforeach
    @endif
    <div class="documentation-desc">{!! $content['desc'] !!}</div>
    @if(isset($content['children']) && count($content['children']) > 0)
        <div>
            @foreach($content['children'] as $child)
                @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map])
            @endforeach
        </div>
    @endif
</div>
