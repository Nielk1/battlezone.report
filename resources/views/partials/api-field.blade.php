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
                <span class="font-icon"><i class="{{ $content['glyph'] }}"></i></span>
            @else
                <span class="svg-icon">{!! File::get(resource_path('svg/' . $content['glyph'] . '.svg')) !!}</span>
            @endif
        @endif
        <h5 class="mb-0">
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
        </h5>
            @if($type_code == 'function')
            <h5 class="mb-0">(</h5>
                @if(isset($content['args']))
                    <span class="d-flex align-items-center flex-row gap-0">
                        @foreach($content['args'] as $loopIndex => $arg)
                            <span class="d-flex align-items-center flex-row gap-0">
                                <span class="me-1">{{ $arg['name'] ?? '???' }}: </span>
                                @if(isset($arg['type']))
                                    @foreach($arg['type'] as $loopIndex2 => $type)
                                        @if(isset($type_id_map[$type]))
                                            <a href="#{{ $type_id_map[$type] }}">{{ $type }}</a>
                                        @else
                                            {{ $type }}
                                        @endif
                                        @if($loopIndex2 < count($arg['type']) - 1)
                                            <span>|</span>
                                        @endif
                                    @endforeach
                                @endif
                            </span>
                            @if($loopIndex < count($content['args']) - 1)
                                <span class="me-1">, </span>
                            @endif
                        @endforeach
                    </span>
                @endif
            <h5 class="mb-0">)</h5>
            @endif

        @foreach($content['type'] as $type)
            @if($type === 'nil' || $type === 'section' || $type === 'function')
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
        {{-- @if(isset($content['tags']))
            @foreach($content['tags'] as $tag => $tagcontent)
                <span class="badge text-bg-info">{{ $tag }}: {{ $tagcontent }}</span>
            @endforeach
        @endif --}}
        {{-- untested deprecated --}}
        @if(isset($content['deprecated']))
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
                    <div class="arg-item ps-3 mb-3 border-5 border-start border-info">
                        ARG:
                        <strong>{{ $arg['name'] ?? '' }}</strong>
                        @if(isset($arg['type']))
                            @foreach($arg['type'] as $loopIndex => $type)
                                @if(isset($type_id_map[$type]))
                                    <a href="#{{ $type_id_map[$type] }}">{{ $type }}</a>
                                @else
                                    {{ $type }}
                                @endif
                                @if($loopIndex < count($arg['type']) - 1)
                                    |
                                @endif
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
                    <div class="arg-item ps-3 mb-3 border-5 border-start border-danger">
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
                    <div class="arg-item ps-3 mb-3 border-5 border-start border-info">
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
                    <div class="arg-item ps-3 mb-3 border-5 border-start border-danger">
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
    <div class="documentation-desc">{!! $content['desc'] !!}</div>
    @if(isset($content['children']) && count($content['children']) > 0)
        <div>
            @foreach($content['children'] as $child)
                @include('partials.api-field', ['content' => $child, 'type_id_map' => $type_id_map])
            @endforeach
        </div>
    @endif
</div>
