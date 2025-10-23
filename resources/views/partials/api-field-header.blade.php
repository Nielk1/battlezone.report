<div class="d-flex flex-row align-items-stretch gap-1 mb-1">
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
            @if($prefix_scope)
                @if(isset($root) && is_array($root) && count($root) > 0)
                    @foreach($root as $idx => $rootPart)
                        <span class="h5">
                            @if($rootPart == null)
                                <span class="root_backlink">_G</span>
                            @else
                                @php($typeRoot = $rootPart['code'])
                                @php($nameRoot = $rootPart['name'])
                                @if(isset($typeRoot))
                                    <a class="root_backlink" href="#{{ $typeRoot }}">{{ $nameRoot }}</a>
                                @else
                                    <span class="root_backlink">{{ $nameRoot }}</span>
                                @endif
                            @endif
                        </span>
                        <span class="h4">.</span>
                    @endforeach
                @endif
            @endif
            <span class="h5">
                @if($type == 'event')"@endif{{ $content['name'] }}@if($type == 'event')"@endif
                <?php
                    if(isset($content['base'])) {
                        preg_match('/^([^\[\]\?]+)([\?\[\]]+)?$/', $content['base'], $matches);
                        $typeBase = $matches[1] ?? $content['base'];
                        $typeSuffix = $matches[2] ?? '';

                        if(($type_code ?? null) == 'type' || ($type_code ?? null) == 'alias' || ($type_code ?? null) == 'enum ref') {
                            echo(' : ');
                            if(isset($type_id_map[$typeBase])) {
                                echo('<a href="#' . $type_id_map[$typeBase] . '">' . e($typeBase) . '</a>' . e($typeSuffix));
                            } else {
                                echo(e($content['base']));
                            }
                        }
                        elseif(($type_code ?? null) == 'enum value' || ($type_code ?? null) == 'type value') {
                            echo(' &lt;');
                            if(isset($type_id_map[$typeBase])) {
                                echo('<a href="#' . $type_id_map[$typeBase] . '">' . e($typeBase) . '</a>' . e($typeSuffix));
                            } else {
                                echo(e($content['base']));
                            }
                            echo('&gt;');
                        }
                    }
                ?>
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
                <span class="badge api_tag text-bg-primary text-wrap"><span class="select-only">[</span>{{ $type }}<span class="select-only">]</span></span>
                {{--<span class="badge text-bg-secondary">{{ $type }}</span>
                <span class="badge text-bg-success">{{ $type }}</span>
                <span class="badge text-bg-danger">{{ $type }}</span>
                <span class="badge text-bg-warning">{{ $type }}</span>
                <span class="badge text-bg-info">{{ $type }}</span>
                <span class="badge text-bg-light">{{ $type }}</span>
                <span class="badge text-bg-dark">{{ $type }}</span>--}}
            @endforeach
            @if($nillable)
                <span class="badge api_tag text-bg-secondary"><span class="select-only">[</span>nil<span class="select-only">]</span></span>
            @endif
            @if(isset($content['global']) && $content['global'])
                <span class="badge api_tag text-bg-success"><span class="select-only">[</span>global<span class="select-only">]</span></span>
            @endif
            @if(isset($content['deprecated']) && $content['deprecated'])
                <span class="badge api_tag text-bg-danger"><span class="select-only">[</span>deprecated<span class="select-only">]</span></span>
            @endif
            @include('partials.api-field-tag', ['tags' => $content['tags']])
        </div>
    </div>
</div>
