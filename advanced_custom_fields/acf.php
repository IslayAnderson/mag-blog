<?php

// START VIBE CODING
function addNestedVariable(array &$tree, array $parts)
{
    $current = &$tree;
    foreach ($parts as $part) {
        if (!isset($current[$part]) || !is_array($current[$part])) {
            $current[$part] = []; // Ensure it's always an array
        }
        $current = &$current[$part];
    }
}


function extractTwigVariablesNested(string $templatePath): array
{
    if (!file_exists($templatePath)) {
        throw new Exception("File not found: $templatePath");
    }

    $template = file_get_contents($templatePath);
    $variablesTree = [];

    // Match {{ ... }}
    preg_match_all('/{{\s*(.*?)\s*}}/', $template, $matches1);

    // Match {% set var = ... %}
    preg_match_all('/{%\s*set\s+(\w+)\s*=\s*.*?%}/', $template, $matches2);

    // Match {% for item in list %}
    preg_match_all('/{%\s*for\s+(\w+)\s+in\s+([\w\.]+)\s*%}/', $template, $matches3);

    $rawPaths = [];

    // From {{ ... }}
    foreach ($matches1[1] as $expr) {
        $expr = preg_replace('/\|.*/', '', $expr); // Remove filters
        // Match variables and attributes like user.name.first or cart.items[0].name
        preg_match_all('/[a-zA-Z_][\w]*(?:\.[a-zA-Z_][\w]*)*/', $expr, $submatches);
        foreach ($submatches[0] as $path) {
            $parts = explode('.', $path);
            addNestedVariable($variablesTree, $parts);
        }
    }

    // From {% set var = ... %}
    foreach ($matches2[1] as $var) {
        addNestedVariable($variablesTree, [$var]);
    }

    // From {% for item in list %}
    foreach ($matches3[1] as $loopVar) {
        addNestedVariable($variablesTree, [$loopVar]);
    }
    foreach ($matches3[2] as $loopSource) {
        $parts = explode('.', $loopSource);
        addNestedVariable($variablesTree, $parts);
    }

    return $variablesTree;
}

//END VIBE CODING

function key_truncator($key)
{
    return str_split($key, 20)[0];
}


$layouts = array();
$vars = array();

foreach (glob(__DIR__ . '/../assets/components/**/template.twig') as $filename) {

    $filename_split = explode('/', $filename);
    $filename_key = $filename_split[count($filename_split) - 2];
    $sub_fields = [];

    try {
        $twigVars = extractTwigVariablesNested($filename);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }

    $vars[$filename_key] = $twigVars;


    foreach ($twigVars as $key => $twigVar) {
        $sub_fields[] = array(
            'key' => key_truncator("field_" . md5($key)),
            'label' => preg_replace('/([A-Z])|(-)/', " $1", $key),
            'name' => $key,
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        );
    }

    $layouts[key_truncator("layout_" . md5($filename_key))] = array(
        'key' => key_truncator("layout_" . md5($filename_key)),
        'name' => key_truncator($filename_key),
        'label' => preg_replace('/([A-Z])|(-)/', " $1", $filename_key),
        'display' => 'block',
        'sub_fields' => $sub_fields,
        'min' => '',
        'max' => ''
    );
}


$page_builder = array(
    array(
        'key' => key_truncator("group_" . md5('components')),
        'title' => 'components',
        'fields' => array(
            array(
                'key' => key_truncator("field_" . md5('content')),
                'label' => 'Content',
                'name' => 'content',
                'aria-label' => '',
                'type' => 'flexible_content',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layouts' => (array)$layouts,
                'min' => '',
                'max' => '',
                'button_label' => 'Add Row',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(
            0 => 'the_content',
            1 => 'discussion',
            2 => 'comments',
            3 => 'format',
            4 => 'send-trackbacks',
        ),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    )
);


$style_checksum_generated = md5(json_encode($page_builder));

if (!file_exists(__DIR__ . '/checksum')) {
    $style_checksum = '';
} else {
    $style_checksum = file_get_contents(__DIR__ . '/checksum');
}

if ($style_checksum != $style_checksum_generated) {
    file_put_contents(__DIR__ . '/checksum', $style_checksum_generated);
    file_put_contents(__DIR__ . '/acf-fields.json', json_encode($page_builder));
}


add_filter('acf/settings/load_json', function () {
    return __DIR__ . '/acf-fields.json';
});


