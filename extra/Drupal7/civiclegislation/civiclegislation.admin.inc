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
            '#title'         => 'Civic Legislation Website',
            '#description'   => 'The URL to your installation of the Civic Legislation web application.',
            '#required'      => true
        ],
        'civiclegislation_cmis_url' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_cmis_url'),
            '#title'         => 'CMIS endpoint',
            '#description'   => 'The server where your boards and commission documents are stored.  This should be the full url to CMIS 1.1 browser binding.',
            '#required'      => true
        ],
        'civiclegislation_cmis_repositoryId' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_cmis_repositoryId'),
            '#title'         => 'CMIS Repository ID',
            '#description'   => 'The ID of your CMIS repository.',
            '#required'      => true
        ],
        'civiclegislation_cmis_username' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_cmis_username'),
            '#title'         => 'CMIS Username',
            '#description'   => 'A username that should have read-only access to your CMIS server',
            '#required'      => true
        ],
        'civiclegislation_cmis_password' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_cmis_password'),
            '#title'         => 'CMIS Password',
            '#description'   => 'The password for the read-only account on your CMIS server',
            '#required'      => true
        ],
        'civiclegislation_solr_url' => [
            '#type'          => 'textfield',
            '#default_value' => variable_get('civiclegislation_solr_url'),
            '#title'         => 'Solr Server',
            '#description'   => "The URL for CMIS server's Solr index.  This is usually the same server as the CMIS endpoint.",
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
    if (!empty($form_state['value']['civiclegislation_url'])) {
        $r = _civiclegislation_get($form_state['values']['civiclegislation_url'].'/committees');
        $json = json_decode($r);
        if (!$json) {
            form_set_error('civiclegislation_url', 'Cannot connect to Civic Legislation URL');
        }
    }
    
    if (($library = libraries_load('chemistry')) && !empty($library['loaded'])) {
        if (   !empty($form_state['values']['civiclegislation_cmis_url'])
            && !empty($form_state['values']['civiclegislation_cmis_username'])
            && !empty($form_state['values']['civiclegislation_cmis_password'])
            && !empty($form_state['values']['civiclegislation_cmis_repositoryId'])) {
            try {
                $service = new CMISService(
                    $form_state['values']['civiclegislation_cmis_url'],
                    $form_state['values']['civiclegislation_cmis_username'],
                    $form_state['values']['civiclegislation_cmis_password'],
                    $form_state['values']['civiclegislation_cmis_repositoryId']
                );
            }
            catch (Exception $e) {
                form_set_error('civiclegislation_cmis_url', 'Cannot connect to CMIS server.  Make sure the URL is valid.');
            }
        }
    }
    else {
        form_set_error('civiclegislation_library', 'Failed to load Chemistry library');
    }
}
