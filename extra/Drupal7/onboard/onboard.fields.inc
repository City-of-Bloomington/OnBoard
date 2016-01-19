<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
/**
 * @implements hook_field_info()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_info/7
 */
function onboard_field_info()
{
    return [
        'onboard_links' => [
            'label'            => 'OnBoard Links',
            'description'      => 'Stores configuration integrating with a CMIS server storing documents related to a board or commission.',
            'default_widget'   => 'onboard_links_fieldset',
            'default_formatter'=> 'onboard_links_formatter'
        ]
    ];
}

/**
 * @implements hook_field_widget_info()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_widget_info/7
 */
function onboard_field_widget_info()
{
    return [
        'onboard_links_fieldset' => [
            'label'       => 'OnBoard Fieldset',
            'field types' => ['onboard_links']
        ]
    ];
}

/**
 * @implements hook_field_is_empty()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_is_empty/7
 */
function onboard_field_is_empty($item, $field)
{
    return empty($item['onboard_links_fieldset']['folder']);
}

/**
 * @implements hook_field_widget_form()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_widget_form/7
 */
function onboard_field_widget_form(&$form, &$form_state, &$field, &$instance, &$langcode, &$items, &$delta, &$element)
{
    if ($instance['widget']['type'] == 'onboard_links_fieldset') {
        $element['onboard_links_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $element['#title'],
            '#tree' => true,
            'folder' => [
                '#type'     => 'textfield',
                '#title'    => 'CMIS Folder',
                '#required' => $element['#required'],
                '#default_value' => isset($items[$delta]['folder']) ? $items[$delta]['folder'] : ''
            ],
            'doctypes_meetings' => [
                '#type'     => 'textfield',
                '#title'    => 'Meeting materials document types',
                '#default_value' => isset($items[$delta]['doctypes_meetings']) ? $items[$delta]['doctypes_meetings'] : ''
            ],
            'doctypes_legislation' => [
                '#type'     => 'textfield',
                '#title'    => 'Legislation document types',
                '#default_value' => isset($items[$delta]['doctypes_legislation']) ? $items[$delta]['doctypes_legislation'] : ''
            ],
            'doctypes_reports' => [
                '#type'     => 'textfield',
                '#title'    => 'Reports document types',
                '#default_value' => isset($items[$delta]['doctypes_reports']) ? $items[$delta]['doctypes_reports'] : ''
            ],
        ];
    }
    return $element;
}

function onboard_field_presave(&$entity_type, &$entity, &$field, &$instance, &$lang, &$items)
{
    if ($instance['widget']['type'] == 'onboard_links_fieldset') {
        $fields = ['folder', 'doctypes_meetings', 'doctypes_legislation', 'doctypes_reports'];
        foreach ($items as $i => $item) {
            foreach ($fields as $f) {
                if (isset($item['onboard_links_fieldset'][$f])) {
                    $items[$i][$f] = $item['onboard_links_fieldset'][$f];
                }
            }
        }
    }
}

/**
 * @implements hook_field_formatter_info()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_formatter_info/7
 */
function onboard_field_formatter_info()
{
    return [
        'onboard_links_formatter' => [
            'label'       => t('Default'),
            'field types' => ['onboard_links']
        ]
    ];
}

/**
 * @implements hook_field_formatter_view()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_formatter_view/7
 */
function onboard_field_formatter_view($entity_type, $entity, $field, $instance, $lang, &$items, &$display)
{
    $element = [];

    switch ($display['type']) {
        case 'onboard_links_formatter':
            $uri = "onboard/{$entity->nid}/$field[field_name]";
            $class_current = ['attributes' => ['class' => ['current']]];

            foreach ($items as $i => $item) {
                $html = '';
                foreach (['meetings', 'legislation', 'reports'] as $type) {
                    if (!empty($item["doctypes_$type"])) {
                        $attr = (current_path() === "$uri/$type") ? $class_current : [];
                        $html.= l(ucfirst($type), "$uri/$type", $attr);
                    }
                }
                $element[$i] = ['#markup' => $html];
            }
        break;
    }
    return $element;
}
