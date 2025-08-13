<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\ChannelButton;
use League\CommonMark\CommonMarkConverter;

class ApiDocController extends Controller
{
    private CommonMarkConverter $converter;
    private const VERSION_TAG_PATTERN = '/\{VERSION[: ]\s*([\d\w\.]+\+?)\}/';
    private const INTERNAL_USE_TAG_PATTERN = '/\{INTERNAL USE\}/';
    private const MULTIPLAYER_TAG_PATTERN = '/\{(?<tagtype>\((i|!|!!)\))(?<tagname>.*)\1[: ]?\s+(?<tagdesc>[^}]+)\}/';
    private const MOD_TAG_PATTERN = '/\{MOD:(?:(?<tagtype>[^:}]{1,50}):\s*(?<tagdesc>[^}]+)|(?<tagtype2>\S+)\s+(?<tagdesc2>[^}]+))\}/';

    public function __construct()
    {
        $this->converter = new CommonMarkConverter();
    }

    private static function sortUnderscoresLast($a, $b)
    {
        $a_cmp = str_replace('_', '{', $a);
        $b_cmp = str_replace('_', '{', $b);
        return strcmp($a_cmp, $b_cmp);
    }

    private static function getSortIndexForType($type, $name)
    {
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

    private static function getTypesForTypeString($type_string)
    {
        $types = [];
        $has_nil = false;
        foreach (explode('|', $type_string) as $type) {
            $type = trim($type);
            if ($type === 'nil') {
                $has_nil = true;
                continue;
            }
            if (str_ends_with($type, '?')) {
                $has_nil = true;
                $type = substr($type, 0, -1);
            }

            // likely will remove these in the future
            //if (str_starts_with($type, 'enum ')) {
            //    $type = 'enum';
            //}
            //if (str_starts_with($type, 'fun(')) {
            //    $type = 'function';
            //}
            //if (str_starts_with($type, 'table<')) {
            //    $type = 'table';
            //}

            $types[] = $type;
        }

        $types = array_unique($types);
        sort($types);
        if ($has_nil) {
            if (count($types) === 1 && $types[0] !== 'nil') {
                $types[0] .= '?';
            } else {
                $types[] = 'nil';
            }
        }

        return $types;
    }

    private static function sortFieldByProperties($a, $b)
    {
        $a_cmp = self::getSortIndexForType($a['type'], $a['name']);
        $b_cmp = self::getSortIndexForType($b['type'], $b['name']);

        if ($a_cmp !== $b_cmp) {
            return strcmp($a_cmp, $b_cmp);
        }

        $a_cmp = str_replace('_', '{', $a['name']);
        $b_cmp = str_replace('_', '{', $b['name']);
        return strcmp($a_cmp, $b_cmp);
    }

    private function generateChannelAndContentFromFieldEntry($field, $special, $section_code, $sections_data)
    {
        $sections = $this->buildSections($section_code, $sections_data);

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
                case 'function_overload':
                    $glyph = "glyph/tablericons/math-function-y";
                    $typeArray[] = "function_overload";
                    break;
                case 'function_callback':
                    $glyph = "glyph/tablericons/function";
                    $typeArray[] = "function_callback";
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
                    $typeArray[] = "UNK_" . $type;
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
        $code = $field['code'] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '_', $name));
        if ($special == 'type') {
            $code = "TYPE-" . $code;
        }
        if (!isset($glyph)) {
            $glyph = "bi bi-question-circle";
            $name = $field['name'] . ":" . ($field['type'] ?? 'unknown') . ($special ? " ($special)" : '');
        }
        if ($section_code) {
            $code = "{$section_code}/" . $code;
        }
        //$members = [];
        //$content_members = [];
        if (isset($field['fields'])) {
            foreach ($field['fields'] as $inner_field) {
                $start = $inner_field['start'] ?? 0;
                $section_key = $this->findSectionKey($sections, $start);

                [$memberChannel, $memberContent] = $this->generateChannelAndContentFromFieldEntry($inner_field, "inner_" . $special, $code, $sections_data);
                //$members[] = $memberChannel;
                //$content_members[] = $memberContent;
                $sections[$section_key]['children'][] = $memberChannel;
                $sections[$section_key]['content'][] = $memberContent;
            }
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
                [$section_tags, $section_desc_html] = $this->extractTagsAndDescription($section_desc);

                $children[] = new Channel(
                    $section_name,
                    $section['desc'] ?? null,
                    'glyph/tablericons/section',
                    null, null,
                    $section_code ? "#{$section_code}" : null,
                    [],
                    $section['children']
                );
                $content_children[] = [
                    'name' => $section_name,
                    'code' => $section_code,
                    'desc' => $section_desc_html,
                    'tags' => $section_tags,
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

            // consider flattening the content as well, but only if the category is really useless
            $content_data = $content_children[0];
            //if ($content_data['name'] === 'Other' && empty($content_data['desc']) && empty($content_data['tags'])) {
                $content_children = $content_data['children'];
            //}
        }

        $desc = $field['desc'] ?? null;
        [$tags, $desc_html] = $this->extractTagsAndDescription($desc);

        // distinct and sort $typeArray
        $typeArray = array_values(array_unique($typeArray));
        sort($typeArray);

        $args = [];
        if (isset($field['args'])) {
            foreach ($field['args'] as $arg) {
                [$arg_tags, $arg_desc_html] = $this->extractTagsAndDescription($arg['desc'] ?? null);
                $args[] =[
                    'name' => $arg['name'] ?? null,
                    'type' => self::getTypesForTypeString($arg['type']),
                    'desc' => $arg_desc_html,
                    'tags' => $arg_tags,
                ];
            }
        }

        $returns = [];
        if (isset($field['returns'])) {
            foreach ($field['returns'] as $return) {
                [$arg_tags, $arg_desc_html] = $this->extractTagsAndDescription($return['desc'] ?? null);
                $returns[] =[
                    'name' => $return['name'] ?? null,
                    'type' => self::getTypesForTypeString($return['type']),
                    'desc' => $arg_desc_html,
                    'tags' => $arg_tags,
                ];
            }
        }

        $content_item = [
            'name' => $name,
            'desc' => $desc_html,
            //'type' => $field['type'] ?? null,
            'type' => $typeArray ?? [],
            'code' => $code,
            'special' => $special,
            'glyph' => $glyph,

            'base' => $field['base'] ?? null,
            'deprecated' => $field['deprecated'] ?? false,
            'global' => $field['global'] ?? false,

            'view' => $field['view'] ?? null, // not used yet?
            'tags' => $tags,

            'args' => $args ?? null,
            'returns' => $returns ?? null,

            //'children' => $content_members
            'children' => $content_children
        ];
        $buttons = [];
        if ($field['deprecated'] ?? false) {
            $buttons[] = ChannelButton::FromArray([
                'name' => 'Deprecated',
                'icon' => 'bi bi-ban',
                'href' => '#',
                'attr' => [ 'data-show' => 'always' ]
            ]);
        }
        $channel = new Channel(
            $name,
            null,
            $glyph,
            null,
            null,
            "#{$code}",
            $buttons,
            //$members
            $children
        );
        return [$channel, $content_item];
    }

    /**
     * Extracts tags and returns cleaned description HTML.
     * @param string|null $desc
     * @return array [$tags, $desc_html]
     */
    private function extractTagsAndDescription(?string $desc): array
    {
        $tags = [];
        if ($desc !== null) {
            // Extract version tags
            preg_match_all(self::VERSION_TAG_PATTERN, $desc, $version_matches);
            foreach ($version_matches[1] as $version_value) {
                $tags['version'] = trim($version_value);
            }

            // Extract internal use tags
            preg_match_all(self::INTERNAL_USE_TAG_PATTERN, $desc, $internal_use_matches);
            if (!empty($internal_use_matches[0])) {
                $tags['internal_use'] = true;
            }

            // Extract MOD tags
            preg_match_all(self::MOD_TAG_PATTERN, $desc, $mod_exu_matches);
            foreach ($mod_exu_matches['tagtype'] as $i => $mod_exu_value) {

                if (!empty($mod_exu_matches['tagtype'][$i])) {
                    $tag_type = trim($mod_exu_matches['tagtype'][$i]);
                    $tag_desc = trim($mod_exu_matches['tagdesc'][$i]);
                } else {
                    $tag_type = trim($mod_exu_matches['tagtype2'][$i]);
                    $tag_desc = trim($mod_exu_matches['tagdesc2'][$i]);
                }
                $tags['mod'][] = [ 'name' => trim($tag_type), 'desc' => trim($tag_desc) ];
            }

            // Extract multiplayer tags
            preg_match_all(self::MULTIPLAYER_TAG_PATTERN, $desc, $multiplayer_matches);
            foreach ($multiplayer_matches['tagname'] as $i => $multiplayer_value) {
                $tag_key = $multiplayer_matches['tagname'][$i];
                $tag_type = $multiplayer_matches['tagtype'][$i];
                $tag_desc = $multiplayer_matches['tagdesc'][$i];
                $tags[strtolower($tag_type)][] = [ 'type' => trim($tag_type), 'name' => trim($tag_key), 'desc' => trim($tag_desc) ];
            }

            // Remove Tags
            $desc = preg_replace(self::VERSION_TAG_PATTERN, '', $desc);
            $desc = preg_replace(self::INTERNAL_USE_TAG_PATTERN, '', $desc);
            $desc = preg_replace(self::MOD_TAG_PATTERN, '', $desc);
            $desc = preg_replace(self::MULTIPLAYER_TAG_PATTERN, '', $desc);


            $desc_html = $this->converter->convert(trim($desc));
        } else {
            $desc_html = null;
        }
        return [$tags, $desc_html];
    }

    public function index($api = null)
    {
        // Only allow safe characters in $api
        if (
            !preg_match('/^[a-zA-Z0-9_-]+$/', $api ?? '')
        ) {
            abort(404);
        }

        $jsonPath = resource_path('data/docgen/'.$api.'.json');
        if (!file_exists($jsonPath)) {
            abort(404);
        }
        $api = json_decode(file_get_contents($jsonPath), true);

        $channels = [];
        $contents = [];

        $key_list = array_unique(array_merge(array_keys($api['types']), array_keys($api['fields'])));
        usort($key_list, [self::class, 'sortUnderscoresLast']);

        $type_id_map = [];
        foreach ($key_list as $module) {
            $types = $api['types'][$module] ?? [];
            foreach ($types as $type_name) {
                // Create a lookup: type name => "module/type_name"
                $type_id_map[$type_name] = $module . '/TYPE-' . strtolower(preg_replace('/[^a-z0-9]+/i', '_', $type_name));
            }
        }

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
                    //[$new_item, $new_content_item] = $this->generateChannelAndContentFromFieldEntry($type_data, 'type', $sections[$section_key]['code'] ?? null);
                    [$new_item, $new_content_item] = $this->generateChannelAndContentFromFieldEntry($type_data, 'type', $module ?? null, $api['sections'][$module] ?? []);
                    $sections[$section_key]['children'][] = $new_item;
                    $sections[$section_key]['content'][] = $new_content_item;
                }
            }
            usort($fields, [self::class, 'sortFieldByProperties']);
            foreach ($fields as $field) {
                $start = $field['start'] ?? 0;
                $section_key = $this->findSectionKey($sections, $start);
                //[$channel, $content] = $this->generateChannelAndContentFromFieldEntry($field, 'field', $sections[$section_key]['code'] ?? null);
                [$channel, $content] = $this->generateChannelAndContentFromFieldEntry($field, 'field', $module ?? null, $api['sections'][$module] ?? []);
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
                    [$section_tags, $section_desc_html] = $this->extractTagsAndDescription($section_desc);

                    $children[] = new Channel(
                        $section_name,
                        $section['desc'] ?? null,
                        'glyph/tablericons/section',
                        null, null,
                        $section_code ? "#{$section_code}" : null,
                        [],
                        $section['children']
                    );
                    $content_children[] = [
                        'name' => $section_name,
                        'code' => $section_code,
                        'desc' => $section_desc_html,
                        'tags' => $section_tags,
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

                // consider flattening the content as well, but only if the category is really useless
                $content_data = $content_children[0];
                if ($content_data['name'] === 'Other' && empty($content_data['desc']) && empty($content_data['tags'])) {
                    $content_children = $content_data['children'];
                }
            }

            $name = $api['modules'][$module]['name'] ?? $module;
            $module_desc = $api['modules'][$module]['desc'] ?? null;
            [$module_tags, $module_desc_html] = $this->extractTagsAndDescription($module_desc);

            $channels[] = new Channel($name, null, null, null, null, "#{$module}", [], $children);
            $contents[] = [
                'name' => $name,
                'code' => $module,
                'desc' => $module_desc_html,
                'children' => $content_children
            ];
        }

        return view('apidoc', [
            'channels_header' => 'API Documentation',
            'channels' => $channels,
            'content' => $contents,
            'type_id_map' => $type_id_map,
            'code' => 'apidoc',
            'activeNav' => 'modding',
            'doc_name' => $api['name'] ?? 'API Documentation',
            'scrollSpyAutoScroll' => true,
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

                //'code' => $module . "/" . strtolower(preg_replace('/[^a-z0-9]+/i', '_', $section['name'] ?? "Other")),
                'code' => $module . "/SEC-" . strtolower(preg_replace('/[^a-z0-9]+/i', '_', $section['name'] ?? "Other")),

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
}
