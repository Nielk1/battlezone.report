<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use League\CommonMark\CommonMarkConverter;

class ApiDocController extends Controller
{
    private CommonMarkConverter $converter;
    //private const VERSION_TAG_PATTERN = '/\{VERSION:\s*\d+(?:\.[\da-zA-Z]+)*\+\}/';
    private const VERSION_TAG_PATTERN = '/\{VERSION:\s*([\d\w\.]+\+?)\}/';

    public function __construct()
    {
        $this->converter = new CommonMarkConverter();
    }

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

    private function generateChannelAndContentFromFieldEntry($field, $special, $section_code)
    {
        $glyph = null;
        $typeArrayRaw = [];
        $typeArray = [];
        $specialModulator = null;

        if (isset($field['type'])) {
            $typeArrayRaw[] = $field['type'];
        }

        if (isset($field['types']) && is_array($field['types'])) {
            foreach ($field['types'] as $t) {
                $typeArrayRaw[] = $t;
            }
        }

        $name = $field['name'];
        foreach($typeArrayRaw as $type) {
            $type = $field['type'];
            switch ($type) {
                case 'alias':
                    $glyph = $glyph ?? "bi bi-box-seam";
                    $typeArray[] = "alias";
                    $specialModulator = "alias";
                    break;
                case 'function?':
                    $typeArray[] = "nil";
                case 'function':
                    $glyph = "glyph/tablericons/math-function";
                    $typeArray[] = "function";
                    break;
                case 'string?':
                    $typeArray[] = "nil";
                case 'string':
                    $glyph = "bi bi-fonts";
                    $typeArray[] = "string";
                    break;
                case 'integer?':
                    $typeArray[] = "nil";
                case 'integer':
                    $glyph = "bi bi-123";
                    $typeArray[] = "integer";
                    break;
                case 'number?':
                    $typeArray[] = "nil";
                case 'number':
                    $glyph = "bi bi-hash";
                    $typeArray[] = "number";
                    break;
                case 'table?':
                    $typeArray[] = "nil";
                case 'table':
                    $glyph = "bi bi-table";
                    $typeArray[] = "table";
                    break;
                case 'boolean?':
                    $typeArray[] = "nil";
                case 'boolean':
                    $glyph = "bi bi-toggle-on";
                    $typeArray[] = "boolean";
                    break;
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
        }
        switch ($special) {
            case 'type':
                if ($specialModulator == 'alias') {
                } else {
                    $glyph = $glyph ?? "bi bi-box-fill";
                    $typeArray[] = "type";
                }
                break;
        }
        $name = $field['name'];
        if (!isset($glyph)) {
            $glyph = "bi bi-question-circle";
            $name = $field['name'] . ":" . ($field['type'] ?? 'unknown') . ($special ? " ($special)" : '');
        }
        $code = $section_code ? "{$section_code}/" . strtolower(preg_replace('/[^a-z0-9]+/i', '_', $name)) : null;
        $members = [];
        $content_members = [];
        if (isset($field['fields'])) {
            foreach ($field['fields'] as $inner_field) {
                [$memberChannel, $memberContent] = $this->generateChannelAndContentFromFieldEntry($inner_field, "inner_" . $special, $code);
                $members[] = $memberChannel;
                $content_members[] = $memberContent;
            }
        }
        $desc = $field['desc'] ?? null;

        if ($desc !== null) {
            // extract data from description

            //preg_match_all('/@(\w+)\s+(.*?)(?=\s+@\w+|$)/s', $desc, $matches, PREG_SET_ORDER);
            //foreach ($matches as $match) {
            //    $tag = $match[1];
            //    $content = trim($match[2]);
            //    $field['tags'][$tag] = $content;
            //}

            // Version Tags
            preg_match_all(self::VERSION_TAG_PATTERN, $desc, $version_matches);
            foreach ($version_matches[1] as $version_value) {
                $tag = 'version';
                $field['tags'][$tag] = trim($version_value);
            }
            $desc = preg_replace(self::VERSION_TAG_PATTERN, '', $desc);

            $desc_html = $this->converter->convert(trim($desc));
        } else {
            $desc_html = null;
        }

        // distinct and sort $typeArray
        $typeArray = array_values(array_unique($typeArray));
        sort($typeArray);

        $content_item = [
            'name' => $name,
            'desc' => $desc_html,
            //'type' => $field['type'] ?? null,
            'type' => $typeArray ?? [],
            'code' => $code,
            'special' => $special,
            'glyph' => $glyph,

            'base' => $field['base'] ?? null,
            'view' => $field['view'] ?? null,
            'tags' => $field['tags'] ?? null,

            'children' => $content_members
        ];
        $channel = new Channel(
            $name,
            null,
            $glyph,
            null,
            null,
            "/apidoc#{$code}",
            [],
            $members
        );
        return [$channel, $content_item];
    }

    public function index()
    {
        $jsonPath = resource_path('data/docgen/bz98r_api.json');
        $api = json_decode(file_get_contents($jsonPath), true);

        $channels = [];
        $contents = [];

        $key_list = array_unique(array_merge(array_keys($api['types']), array_keys($api['fields'])));
        usort($key_list, [self::class, 'sortUnderscoresLast']);

        foreach ($key_list as $module) {
            $sections = $this->buildSections($module, $api['sections'][$module] ?? []);
            $types = $api['types'][$module] ?? [];
            $fields = $api['fields'][$module] ?? [];

            // Populate sections with types and fields
            foreach ($types as $type) {
                $type_data = $api['type_data'][$type] ?? null;
                if ($type_data) {
                    $start = $type_data['start'] ?? 0;
                    $section_key = $this->findSectionKey($sections, $start);
                    [$new_item, $new_content_item] = $this->generateChannelAndContentFromFieldEntry($type_data, 'type', $sections[$section_key]['code'] ?? null);
                    $sections[$section_key]['children'][] = $new_item;
                    $sections[$section_key]['content'][] = $new_content_item;
                }
            }
            usort($fields, [self::class, 'sortFieldByProperties']);
            foreach ($fields as $field) {
                $start = $field['start'] ?? 0;
                $section_key = $this->findSectionKey($sections, $start);
                [$channel, $content] = $this->generateChannelAndContentFromFieldEntry($field, 'field', $sections[$section_key]['code'] ?? null);
                $sections[$section_key]['children'][] = $channel;
                $sections[$section_key]['content'][] = $content;
            }

            // Prepare children arrays for output
            $children = [];
            $content_children = [];
            $section_counter = 0;
            foreach ($sections as $section) {
                if (!empty($section['children'])) {
                    $section_name = $section['name'] ?? "Other";
                    $section_code = $section['code'] ?? null;
                    $section_desc = $section['desc'] ?? null;
                    if ($section_desc !== null) {
                        $desc_html = $this->converter->convert(trim($section_desc));
                    } else {
                        $desc_html = null;
                    }
                    $children[] = new Channel(
                        $section_name,
                        $section['desc'] ?? null,
                        'glyph/tablericons/section',
                        null, null,
                        $section_code ? "/apidoc#{$section_code}" : null,
                        [],
                        $section['children']
                    );
                    $content_children[] = [
                        'name' => $section_name,
                        'code' => $section_code,
                        'desc' => $desc_html,
                        'type' => [ 'section' ],
                        'glyph' => 'glyph/tablericons/section',
                        'children' => $section['content'] ?? []
                    ];
                    $section_counter++;
                }
            }
            if ($section_counter === 1) {
                // If there's only one section, we can flatten it
                $children = $children[0]->children;

                // Don't flatten content children, keep structure
                //$content_children = $content_children[0]['children'];
            }

            $name = $api['modules'][$module]['name'] ?? $module;
            $module_desc = $api['modules'][$module]['desc'] ?? null;
            if ($module_desc !== null) {
                $desc_html = $this->converter->convert(trim($module_desc));
            } else {
                $desc_html = null;
            }
            $channels[] = new Channel($name, null, null, null, null, "/apidoc#{$module}", [], $children);
            $contents[] = [
                'name' => $name,
                'code' => $module,
                'desc' => $desc_html,
                'children' => $content_children
            ];
        }

        return view('apidoc.index', [
            'channels_header' => 'Issues',
            'channels' => $channels,
            'content' => $contents
        ]);
    }

    // Helper to build sections array
    private function buildSections($module, $sections)
    {
        $result = [0 => ['name' => null, 'desc' => null, 'children' => [], 'content' => [], 'code' => $module]];
        foreach ($sections as $section) {
            $result[$section['start']] = [
                'name' => $section['name'],
                'desc' => $section['desc'] ?? null,

                'code' => $module . "/" . strtolower(preg_replace('/[^a-z0-9]+/i', '_', $section['name'] ?? "Other")),

                'children' => [],
                'content' => []
            ];
        }
        ksort($result, SORT_NUMERIC);
        return $result;
    }

    // Helper to find the correct section key for a given start value
    private function findSectionKey($sections, $start)
    {
        $keys = array_keys($sections);
        $section_key = 0;
        foreach ($keys as $key) {
            if ($key <= $start) {
                $section_key = $key;
            } else {
                break;
            }
        }
        return $section_key;
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
