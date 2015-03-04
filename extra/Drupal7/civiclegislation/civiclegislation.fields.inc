<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
function civiclegislation_field_info()
{
    return [
        'civiclegislation_cmis' => [
            'label'            => 'CMIS Integration for boards and commission',
            'description'      => 'Stores configuration integrating with a CMIS server storing documents related to a board or commission.',
            'default_widget'   => 'civiclegislation_fieldset',
            'default_formatter'=> 'civiclegislation_default'
        ]
    ];
}

function civiclegislation_field_widget_info()
{
    return [
        'civiclegislation_fieldset' => [
            'label' => 'CMIS Integration',
            'field types' => ['civiclegislation_cmis']
        ]
    ];
}

function civiclegislation_field_is_empty($item, $field)
{
    return empty($item['civiclegislation_fieldset']['folder']);
}

function civiclegislation_field_widget_form(&$form, &$form_state, &$field, &$instance, &$langcode, &$items, &$delta, &$element)
{
    if ($instance['widget']['type'] == 'civiclegislation_fieldset') {
        $element['civiclegislation_fieldset'] = [
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

function civiclegislation_field_presave(&$entity_type, &$entity, &$field, &$instance, &$lang, &$items)
{
    if ($instance['widget']['type'] == 'civiclegislation_fieldset') {
        $fields = ['folder', 'doctypes_meetings', 'doctypes_legislation', 'doctypes_reports'];
        foreach ($items as $i => $item) {
            foreach ($fields as $f) {
                if (isset($item['civiclegislation_fieldset'][$f])) {
                    $items[$i][$f] = $item['civiclegislation_fieldset'][$f];
                }
            }
        }
    }
}

function civiclegislation_field_formatter_info()
{
    return [
        'civiclegislation_default' => [
            'label' => t('Default'),
            'field types' => ['civiclegislation_fieldset']
        ]
    ];
}

function civiclegislation_field_formatter_view($entity_type, $entity, $field, $instance, $lang, &$items, &$display)
{
    $element = [];

    switch ($display['type']) {
        case 'civiclegislation_default':
            foreach ($items as $i => $item) {
                $html = "<h3>$item[folder]</h3>";
                if (!empty($item['doctypes_meetings'])) { $html.="<div>$item[doctypes_meetings]</div>"; }
                if (!empty($item['doctypes_legislation'])) { $html.="<div>$item[doctypes_legislation]</div>"; }
                if (!empty($item['doctypes_reports'])) { $html.= "<div>$item[doctypes_reports]</div>"; }
                $element[$i] = ['#markup' => $html];
            }
        break;
    }
    return $element;
}
