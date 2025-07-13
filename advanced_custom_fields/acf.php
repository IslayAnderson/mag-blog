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
//    $current['_leaf'] = true; // Mark leaf node
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


$layouts = array();
$vars = array();

foreach (glob(__DIR__ . '/../assets/components/**/template.twig') as $filename) {

    $filename_split = explode('/', $filename);
    $sub_fields = [];

    try {
        $twigVars = extractTwigVariablesNested($filename);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }

    $vars[$filename_split[count($filename_split)-2]] = $twigVars;

    foreach ($twigVars as $key => $twigVar) {
//        var_dump($twigVar);
        $sub_fields[] = array(
            'key' => $key,
            'label' => 'Text',
            'name' => 'text',
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

    $layouts[] = array(
        $filename_split[count($filename_split)-2] => array(
        'key' => $filename_split[count($filename_split)-2],
        'name' => $filename_split[count($filename_split)-2],
        'label' => $filename_split[count($filename_split)-2],
        'display' => 'block',
        'sub_fields' => $sub_fields,
        'min' => '',
        'max' => '',
    ),);
}

//echo json_encode($vars);




add_action( 'acf/include_fields', function($layouts) {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'page_builder',
        'title' => 'Page Builder',
        'fields' => array(
            array(
                'key' => 'content',
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
                'layouts' => $layouts,
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
        'style' => 'default',
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
    ) );
} );

