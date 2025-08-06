<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;

class ApiDocController extends Controller
{
    private static function sortUnderscoresLast($a, $b) {
        $a_cmp = str_replace('_', '{', $a);
        $b_cmp = str_replace('_', '{', $b);
        return strcmp($a_cmp, $b_cmp);
    }

    private static function getSortIndexForType($type, $name) {
        switch ($type) {
            case 'table':
            case 'table?':
                return 1;
            case 'enum': // don't think this can happen
                return 2;
            case 'string':
            case 'string?':
                return 3;
            case 'integer':
            case 'integer?':
                return 4;
            case 'number':
            case 'number?':
                return 5;
            case 'boolean':
            case 'boolean?':
                return 6;
            case 'function':
                return 8;
            default:
                if (str_starts_with($type, 'enum ')) {
                    return 2; // enum
                }
                if (str_starts_with($type, 'fun(')) {
                    return 8; // function
                }
                if (str_starts_with($type, 'table<')) {
                    return 1; // table
                }
                return 9999; // unknown type
        }

        switch($name)
        {
            case '[integer]':
            case '[number]':
            case '[string]':
                return 7;
        }

        return 9999;
    }

    private static function sortFieldByProperties($a, $b) {
        $a_cmp = self::getSortIndexForType($a['type'], $a['name']);
        $b_cmp = self::getSortIndexForType($b['type'], $b['name']);

        if ($a_cmp !== $b_cmp) {
            return strcmp($a_cmp, $b_cmp);
        }

        $a_cmp = str_replace('_', '{', $a['name']);
        $b_cmp = str_replace('_', '{', $b['name']);
        return strcmp($a_cmp, $b_cmp);
    }

    private function generateChannelFromFieldEntry($field, $special, &$new_content_item)
    {
        $glyph = null;
        if (isset($field['type'])) {
            $type = $field['type'];
            $name = $field['name'];
            switch ($type) {
                case 'function':
                case 'function?':
                    $glyph = "glyph/tablericons/math-function";
                    break;
                case 'string':
                case 'string?':
                    $glyph = "bi bi-fonts";
                    break;
                case 'integer':
                case 'integer?':
                    $glyph = "bi bi-123";
                    break;
                case 'number':
                case 'number?':
                    $glyph = "bi bi-hash";
                    break;
                case 'table':
                case 'table?':
                    $glyph = "bi bi-table";
                    break;
                //case 'bool':
                case 'boolean':
                case 'boolean?':
                    $glyph = "bi bi-toggle-on";
                    break;
                //case 'array':
                //case 'list':
                //    $glyph = "bi bi-list-ul";
                //    break;
                //case 'object':
                //    $glyph = "bi bi-box-seam";
                //    break;
                //case 'callable':
                //    $glyph = "bi bi-code-slash";
                //    break;
                //case 'mixed':
                //    $glyph = "bi bi-question-circle";
                //    break;
                default:
                    if (str_starts_with($type, 'enum ')) {
                        $glyph = "bi bi-list-ul";
                        break;
                    }
                    if (str_starts_with($type, 'fun(')) {
                        $glyph = "glyph/tablericons/math-function";
                        break;
                    }
                    if (str_starts_with($type, 'table<')) {
                        $glyph = "bi bi-table";
                        break;
                    }
                    if ($name == '[integer]') {
                        $glyph = "bi bi-braces";
                        break;
                    }
                    if ($name == '[number]') {
                        $glyph = "bi bi-braces";
                        break;
                    }
                    if ($name == '[string]') {
                        $glyph = "bi bi-braces";
                        break;
                    }
                    break;
            }
        } else {
            switch ($special) {
                //case 'type_field':
                //    $glyph = "bi bi-code-square";
                //    break;
                case 'type':
                    $glyph = "bi bi-box-seam";
                    break;
                //case 'field':
                //    $glyph = "bi bi-file-earmark-text";
                //    break;
            }
        }
        $members = [];
        $content_members = [];
        if (isset($field['fields'])) {
            foreach ($field['fields'] as $inner_field) {
                //$members[] = $this->generateChannelFromFieldEntry($inner_field, 'inner_field');
                $content_member = [];
                $members[] = $this->generateChannelFromFieldEntry($inner_field, "inner_" . $special, $content_member);
                $content_members[] = $content_member;
            }
        }
        $name = $field['name'];
        if (!isset($glyph)) {
            $glyph = "bi bi-question-circle";
            $name = $field['name'] . ":" . ($field['type'] ?? 'unknown') . ($special ? " ($special)" : '');
        }
        $new_content_item = [
            'name' => $name,
            'desc' => $field['desc'] ?? null,
            'type' => $field['type'] ?? null,
            'special' => $special,
            'glyph' => $glyph,
            'children' => $content_members
        ];
        return new Channel(
            $name,
            null,
            $glyph,
            null,
            null,
            null,
            [],
            $members
        );
    }

    public function index()
    {
        $jsonPath = resource_path('data/docgen/bz98r_api.json');
        $api = json_decode(file_get_contents($jsonPath), true);

        $channels = [];
        $contents = [];

        //$key_list = array_keys($api['types']) +
        //    array_keys($api['fields']);
        //    array_keys($api['modules']) +
        //    array_keys($api['functions']) +
        //    array_keys($api['constants']);

        $key_list = array_unique(array_merge(array_keys($api['types']), array_keys($api['fields'])));

        // Custom sort: underscores sort after "z"
        usort($key_list, [self::class, 'sortUnderscoresLast']);

        foreach ($key_list as $module) {
            $section_map_table = [ 0 => [
                'children' => []
            ] ];
            $section_map_content = [ 0 => [
                'children' => []
            ] ];
            if (isset($api['sections'][$module])) {
                foreach ($api['sections'][$module] as $section) {
                    $section_map_table[$section['start']] = [
                        'name' => $section['name'],
                        'desc' => $section['desc'] ?? null,
                        'children' => []
                    ];
                    $section_map_content[$section['start']] = [
                        'name' => $section['name'],
                        'desc' => $section['desc'] ?? null,
                        'children' => []
                    ];
                }
            }
            ksort($section_map_table, SORT_NUMERIC);
            ksort($section_map_content, SORT_NUMERIC);

            $types = $api['types'][$module] ?? [];
            $fields = $api['fields'][$module] ?? [];

            //$children_types = []; // types and fields
            //$children_fields = []; // types and fields

            // add type children to module
            //usort($types, [self::class, 'sortFieldByProperties']);
            foreach ($types as $type) {
                //$members = [];

                if (isset($type) && isset($api['type_data'][$type])) {
                    $type_data = $api['type_data'][$type];

                    $start = $type_data['start'] ?? 0;
                    $target = &$section_map_table[0]['children'];
                    $content_target = &$section_map_content[0]['children'];
                    foreach ($section_map_table as $section_start => &$section_data) {
                        if ($section_start <= $start) {
                            $target = &$section_data['children'];
                            $content_target = &$section_map_content[$section_start]['children'];
                        } else {
                            break;
                        }
                    }
                    unset($section_data);

                    //foreach ($type_data['fields'] as $field) {
                    //    $members[] = $this->generateChannelFromFieldEntry($field, 'type_field');
                    //}
                    $new_content_item = [];
                    $new_item = $this->generateChannelFromFieldEntry($type_data, 'type', $new_content_item);
                    //$new_item->children = array_merge($new_item->children ?? [], $members);
                    $target[] = $new_item;
                    $content_target[] = $new_content_item;
                }else{
                    // fallback for types without data
                    $target[] = new Channel(
                        $type,
                        null,
                        "bi bi-exclamation-diamond-fill",
                        null,
                        null,
                        null,
                        [],
                        []//$members
                    );
                }
            }

            // add field children to file
            usort($fields, [self::class, 'sortFieldByProperties']);
            foreach ($fields as $field) {
                //$children_fields[] = $this->generateChannelFromFieldEntry($field, 'field');

                $start = $field['start'] ?? 0;
                $target = &$section_map_table[0]['children'];
                $content_target = &$section_map_content[0]['children'];
                foreach ($section_map_table as $section_start => &$section_data) {
                    if ($section_start <= $start) {
                        $target = &$section_data['children'];
                        $content_target = &$section_map_content[$section_start]['children'];
                    } else {
                        break;
                    }
                }
                unset($section_data);

                $new_content_item = [];
                $target[] = $this->generateChannelFromFieldEntry($field, 'field', $new_content_item);
                $content_target[] = $new_content_item;
            }

            //$children = array_merge($children_types, $children_fields);

            // convert $section_map_table into sections, unless there's only section at 0
            $children = [];
            $content_children = [];
            if (count($section_map_table) > 1) {
                foreach ($section_map_table as $section_start => $section_data) {
                    if (count($section_data['children']) == 0)
                        continue;
                    $children[] = new Channel(
                        $section_data['name'] ?? "Other",
                        $section_data['desc'] ?? null,
                        'glyph/tablericons/section',
                        null,
                        null,
                        null,
                        [],
                        $section_data['children'] ?? []
                    );
                    $content_children[] = [
                        'name' => $section_data['name'] ?? "Other",
                        'desc' => $section_data['desc'] ?? null,
                        'children' => $section_map_content[$section_start]['children'] ?? []
                    ];
                }
            } else {
                $children = $section_map_table[0]['children'] ?? [];
                $content_children = $section_map_content[0]['children'] ?? [];
            }

            $name = $module;
            if (isset($api['modules'][$module]['name'])) {
                $name = $api['modules'][$module]['name'];
            }

            // field item
            $channels[] = new Channel(
                $name,
                null,
                null,
                null,
                null,
                null,
                [],
                $children
                //array_map([Channel::class, 'fromArray'], $entry['children'] ?? [])
            );
            $contents[] = [
                'name' => $name,
                'desc' => $api['modules'][$module]['desc'] ?? null,
                'children' => $content_children
            ];
        }

        return view('apidoc.index', ['channels_header' => 'Issues', 'channels' => $channels, 'content' => $contents]);
    }

    public function show($name)
    {
        $jsonPath = resource_path('data/docgen/bz98r_api.json');
        $api = json_decode(file_get_contents($jsonPath), true);

        $entry = collect($api)->firstWhere('name', $name);

        if (!$entry) {
            abort(404, 'API entry not found');
        }

        return view('apidoc.show', ['entry' => $entry]);
    }
}
