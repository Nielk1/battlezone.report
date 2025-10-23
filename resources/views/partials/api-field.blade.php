<div class="apidoc ps-2 my-3 border-5 border-start border-primary" data-spy="section" id="{{ $content['code'] }}">
    @php($nillable = false)
    @php($has_primitive = false)
    @php($has_non_primitive = false)
    @php($type_code = null)
    @php($prefix_scope = false)
    @php($is_function_overload_dummy = false)
    @if(isset($child['global']) && $child['global'])
        @php($root = [null])
    @elseif(!isset($root))
        @php($root = [])
    @endif
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
            @case('function overload')
                @php($prefix_scope = true)
            @case('event')
            @case('function callback')
                @php($type_code = 'function')
                @php($has_non_primitive = true)
                @break
            @case('function_overload_dummy')
                @php($type_code = 'function')
                @php($has_non_primitive = true)
                @php($is_function_overload_dummy = true)
                @php($prefix_scope = true)
                @break
            {{--@case('table<string, string>')
            @case('table<string, number>')
            @case('table<string, integer>')
            @case('table<string, boolean>')
            @case('table<number, string>')
            @case('table<number, number>')
            @case('table<number, integer>')
            @case('table<number, boolean>')
            @case('table<integer, string>')
            @case('table<integer, number>')
            @case('table<integer, integer>')
            @case('table<integer, boolean>')
            @case('table<boolean, string>')
            @case('table<boolean, number>')
            @case('table<boolean, integer>')
            @case('table<boolean, boolean>')
                @php($type_code = 'table')
                @break
            @case('string[]')
            @case('number[]')
            @case('integer[]')
            @case('boolean[]')
                @php($type_code = 'array')
                @break--}}
            @case('table')
                @php($type_code = 'table')
                @php($prefix_scope = true)
                @break
            @case('enum')
                @php($type_code = 'enum')
                @break
            @case('string')
            @case('integer')
            @case('number')
            @case('boolean')
                @php($has_primitive = true)
                @php($prefix_scope = true)
                @break
        @endswitch
        @if(str_starts_with($type, 'enum value '))
            @php($type_code = 'enum value')
            @php($prefix_scope = true)
        @elseif(str_starts_with($type, 'enum '))
            @php($type_code = 'enum ref')
            @php($prefix_scope = true)
        @endif
        @if(str_starts_with($type, 'type value '))
            @php($type_code = 'type value')
            @php($prefix_scope = true)
        @endif
        @if(str_starts_with($type, 'table<'))
            @php($type_code = 'table')
            @php($prefix_scope = true)
        @endif
        @if(str_ends_with($type, '[]'))
            @php($type_code = 'array')
            @php($prefix_scope = true)
        @endif
        @if($type == 'nil')
            @php($nillable = true)
        @endif
    @endforeach
    <span class="select-only">---------------------------------------------------</span>

    @if($is_function_overload_dummy && isset($content['children']) && count($content['children']) > 0)
        @foreach($content['children'] as $child)
            @include('partials.api-field-header', ['content' => $child, 'type_id_map' => $type_id_map, 'prefix_scope' => $prefix_scope, 'root' => $root])
        @endforeach
    @else
        @include('partials.api-field-header', ['content' => $content, 'type_id_map' => $type_id_map, 'prefix_scope' => $prefix_scope, 'root' => $root])
    @endif

    @if(!($has_primitive && !$has_non_primitive)
        && $type_code != 'enum ref'
        && $type_code != 'enum value'
        && $type_code != 'type value')
        @switch($type_code ?? null)
            @case('type')
            @case('alias')
            @case('section')
                @break
            @case ('function')

                @if(isset($content['hook_add']))
                    {{--<span class="select-only">----</span>--}}
                    <div class="arg-item hook_add mb-1 border-15 border-start">
                        <span class="print-and-select">@add&nbsp;</span><span class="fw-bolder">{{ $content['hook_add'] }}</span>
                    </div>
                @endif

                @if(isset($content['hook_call']))
                    {{--<span class="select-only">----</span>--}}
                    <div class="arg-item hook_call mb-1 border-15 border-start">
                        <span class="print-and-select">@call&nbsp;</span><span class="fw-bolder">{{ $content['hook_call'] }}</span>
                    </div>
                @endif

                @if(isset($content['args']))
                @foreach($content['args'] as $arg)
                    {{--<span class="select-only">----</span>--}}
                    <div class="arg-item param mb-1 border-15 border-start">
                        <span class="print-and-select">@param&nbsp;</span><span class="fw-bolder">{{ $arg['name'] ?? '' }}</span>
                        <?php
                            $typesOut = [];
                            if(isset($arg['type'])) {
                                foreach($arg['type'] as $type) {
                                    preg_match('/^([^\[\]\?]+)([\?\[\]]+)?$/', $type, $matches);
                                    $typeBase = $matches[1] ?? $type;
                                    $typeSuffix = $matches[2] ?? '';

                                    if(isset($type_id_map[$typeBase])) {
                                        $typesOut[] = '<a href="#' . e($type_id_map[$typeBase]) . '">' . e($typeBase) . '</a>' . e($typeSuffix);
                                    } else {
                                        $typesOut[] = e($type);
                                    }
                                }
                            }
                            echo implode('<span class="fw-bold">|</span>', $typesOut);
                        ?>
                        @if(isset($arg['tags']))
                            <div class="d-flex flex-row gap-1">
                                @include('partials.api-field-tag', ['tags' => $arg['tags']])
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
                    {{--<span class="select-only">----</span>--}}
                    <div class="arg-item return mb-1 border-15 border-start position-relative">
                        <span class="print-and-select">@return&nbsp;</span><span class="fw-bolder">{{ $return['name'] ?? '['.$return_index.']' }}</span>
                        <?php
                            $typesOut = [];
                            if(isset($return['type'])) {
                                foreach($return['type'] as $type) {
                                    preg_match('/^([^\[\]\?]+)([\?\[\]]+)?$/', $type, $matches);
                                    $typeBase = $matches[1] ?? $type;
                                    $typeSuffix = $matches[2] ?? '';

                                    if(isset($type_id_map[$typeBase])) {
                                        $typesOut[] = '<a href="#' . e($type_id_map[$typeBase]) . '">' . e($typeBase) . '</a>' . e($typeSuffix);
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
                                @include('partials.api-field-tag', ['tags' => $return['tags']])
                            </div>
                        @endif
                        @if(!empty($return['desc']))
                            {!! $return['desc'] !!}
                        @endif
                    </div>
                @endforeach
                @endif

                @break
            @case('enum')
            @case('table')
            @case('array')
                @break
            @case('any')
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
        {{--<span class="select-only">----</span>--}}
        <div class="alert alert-danger d-flex align-items-center my-1" role="alert">
            <i class="bi bi-exclamation-octagon-fill alert-icon" aria-label="Error:"></i>
            <div>
                <span class="print-and-select float-start"><strong>Error</strong>:&nbsp;</span>
                {!! $tag['desc'] !!}
            </div>
        </div>
    @endforeach
    @endif
    @if(isset($content['tags']['(!)']))
    @foreach($content['tags']['(!)'] as $tag)
        {{--<span class="select-only">----</span>--}}
        <div class="alert alert-warning d-flex align-items-center my-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-icon" aria-label="Warning:"></i>
            <div>
                <span class="print-and-select float-start"><strong>Warning</strong>:&nbsp;</span>
                {!! $tag['desc'] !!}
            </div>
        </div>
    @endforeach
    @endif
    @if(isset($content['tags']['(i)']))
    @foreach($content['tags']['(i)'] as $tag)
        {{--<span class="select-only">----</span>--}}
        <div class="alert alert-info d-flex align-items-center my-1" role="alert">
            <i class="bi bi-info-circle-fill alert-icon" aria-label="Info:"></i>
            <div>
                <span class="print-and-select float-start"><strong>Info</strong>:&nbsp;</span>
                {!! $tag['desc'] !!}
            </div>
        </div>
    @endforeach
    @endif
    @if(isset($content['tags']['mod']))
    @foreach($content['tags']['mod'] as $tag)
        {{--<span class="select-only">----</span>--}}
        <div class="alert alert-light d-flex align-items-center my-1" role="alert">
            <i class="bi bi-tools alert-icon" aria-label="Mod:"></i>
            <div>
                <span class="print-and-select float-start"><strong>Mod</strong>:&nbsp;</span>
                {!! $tag['desc'] !!}
            </div>
        </div>
    @endforeach
    @endif
    {{--<span class="select-only">----</span>--}}
    <div class="documentation-desc">{!! $content['desc'] !!}</div>
    @if(isset($content['condensed']) && $content['condensed'])
        @if(isset($content['children']) && count($content['children']) > 0)
            <span class="select-only">>></span>
            <div>
                @foreach($content['children'] as $child)
                    {{--@include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map])--}}
                    {{--<span class="select-only">----</span>--}}
                    <div class="arg-item enum-field mb-1 border-15 border-start">
                        <span class="print-and-select">@field&nbsp;</span><span class="fw-bolder">{{ $child['name'] ?? '' }}</span>
                        <span> = {{ $child['value'] ?? '' }}: </span>
                        <?php
                            $typesOut = [];
                            if(isset($child['type'])) {
                                foreach($child['type'] as $type) {
                                    preg_match('/^([^\[\]\?]+)([\?\[\]]+)?$/', $type, $matches);
                                    $typeBase = $matches[1] ?? $type;
                                    $typeSuffix = $matches[2] ?? '';

                                    if(isset($type_id_map[$typeBase])) {
                                        $typesOut[] = '<a href="#' . e($type_id_map[$typeBase]) . '">' . e($typeBase) . '</a>' . e($typeSuffix);
                                    } else {
                                        $typesOut[] = e($type);
                                    }
                                }
                            }
                            echo implode('<span class="fw-bold">|</span>', $typesOut);
                        ?>
                        @if(isset($child['tags']))
                            <div class="d-flex flex-row gap-1">
                                @include('partials.api-field-tag', ['tags' => $child['tags']])
                            </div>
                        @endif
                        @if(!empty($child['desc']))
                            {!! $child['desc'] !!}
                        @endif
                    </div>
                @endforeach
            </div>
            <span class="select-only"><<</span>
        @endif
    @elseif(!$is_function_overload_dummy && isset($content['children']) && count($content['children']) > 0)
        <span class="select-only">>></span>
        <div>
            @if($type_code == 'type')
                @foreach($content['children'] as $child)
                    @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map, 'root' => [$content]])  {{-- Types reset scope for display --}}
                @endforeach
            @elseif($type_code == 'table' || $type_code == 'array')
                @foreach($content['children'] as $child)
                    @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map, 'root' => array_merge($root, [$content])])
                @endforeach
            @else
                @foreach($content['children'] as $child)
                    @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map, 'root' => $root])
                @endforeach
            @endif
        </div>
        <span class="select-only"><<</span>
    @endif
</div>
