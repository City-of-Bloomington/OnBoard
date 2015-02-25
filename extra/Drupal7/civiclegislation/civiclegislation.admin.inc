<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
function civiclegislation_admin_form()
{
    return system_settings_form([
        'civiclegislation_url' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_url'),
            '#title'         => 'CMIS endpoint',
            '#description'   => 'The server where your boards and commission documents are stored.  This should be the full url to CMIS 1.1 browser binding.',
            '#required'      => true
        ],
        'civiclegislation_repositoryId' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_repositoryId'),
            '#title'         => 'CMIS Repository ID',
            '#description'   => 'The ID of your CMIS repository.',
            '#required'      => true
        ],
        'civiclegislation_username' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_username'),
            '#title'         => 'CMIS Username',
            '#description'   => 'A username that should have read-only access to your CMIS server',
            '#required'      => true
        ],
        'civiclegislation_password' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_password'),
            '#title'         => 'CMIS Password',
            '#description'   => 'The password for the read-only account on your CMIS server',
            '#required'      => true
        ]
    ]);
}

/**
 * Verifies the connection to the CMIS server
 *
 * @implements hook_admin_validate()
 * @see https://api.drupal.org/api/drupal/modules!node!node.api.php/function/hook_validate/7
 */
function civiclegislation_admin_form_validate($form, &$form_state)
{
    if (($library = libraries_load('chemistry')) && !empty($library['loaded'])) {
        if (   !empty($form_state['values']['civiclegislation_url'])
            && !empty($form_state['values']['civiclegislation_username'])
            && !empty($form_state['values']['civiclegislation_password'])
            && !empty($form_state['values']['civiclegislation_repositoryId'])) {
            try {
                $service = new CMISService(
                    $form_state['values']['civiclegislation_url'],
                    $form_state['values']['civiclegislation_username'],
                    $form_state['values']['civiclegislation_password'],
                    $form_state['values']['civiclegislation_repositoryId']
                );
            }
            catch (Exception $e) {
                form_set_error('civiclegislation_url', 'Cannot connect to CMIS server.  Make sure the URL is valid.');
            }
        }
    }
    else {
        form_set_error('civiclegislation_library', 'Failed to load Chemistry library');
    }
}
