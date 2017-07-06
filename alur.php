<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package		Jea
 * @copyright	Copyright (C) 2015 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.event.plugin');

class plgJeaAlur extends JPlugin
{
    /**
     * Constructor
     *
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }
    
    /**
     * onBeforeSaveProperty method
     *
     * @param string $namespace
     * @param TableProperties $row
     * @param boolean $is_new
     * @return boolean
     */
    public function onBeforeSaveProperty($namespace, $row, $is_new)
    {
        $input = JFactory::getApplication()->input;
        $emptyData = true;

        $data = array(
            'taux_honoraires' => '',
            'hon_acq' => 0,
            'copropriete' => 0,
            'nb_lots_copropriete' => 0,
            'montant_quote_part' => 0,
            'procedure_syndicat' => 0,
            'detail_procedure' => ''
        );

        foreach ($data as $k => $v) {
            $data[$k] = $input->getString($k, $v);
            if (!empty($data[$k])) {
                $emptyData = false;
            }
        }

        if ($emptyData) {
            return;
        }

        $row->alur = json_encode($data);

        return true;
    }

    /**
     * onBeforeEndPane method (Called in the admin property form)
     *
     * @param TableProperties $row
     * @return void
     */
    public function onBeforeEndPanels(&$row)
    {
        $data = array(
            'taux_honoraires' => '',
            'hon_acq' => 0,
            'copropriete' => 0,
            'nb_lots_copropriete' => 0,
            'montant_quote_part' => 0,
            'procedure_syndicat' => 0,
            'detail_procedure' => ''
        );

        if (!empty($row->alur)) {
            $data = json_decode($row->alur);
        }


        $html ='
        <fieldset class="panelform">
          <ul class="adminformlist">
            <li>
              <label for="taux_honoraires" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_TAUX_HONORAIRES_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_TAUX_HONORAIRES') .' : </label>
              <input type="text" name="taux_honoraires" id="taux_honoraires" value="'. $data->taux_honoraires .'" class="numberbox" size="5" />
            </li>
            <li>
              <label for="hon_acq" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_HON_ACQ_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_HON_ACQ') .' : </label>
              <input type="checkbox" name="hon_acq" id="hon_acq" value="1" '. ( $data->hon_acq ? 'checked' : '' ) .' />
            </li>
            <li>
              <label for="copropriete" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE') .' : </label>
              <input type="checkbox" name="copropriete" id="copropriete" value="1" '. ( $data->copropriete ? 'checked' : '' ) .' />
            </li>
            <li>
              <label for="nb_lots_copropriete" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_NB_LOTS_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_NB_LOTS') .' : </label>
              <input type="text" name="nb_lots_copropriete" id="nb_lots_copropriete" value="'. $data->nb_lots_copropriete .'" class="numberbox" size="5" />
            </li>
            <li>
              <label for="montant_quote_part" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_CHARGES_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_CHARGES') .' : </label>
              <input type="text" name="montant_quote_part" id="montant_quote_part" value="'. $data->montant_quote_part .'" class="numberbox" size="5" />
            </li>
            <li>
              <label for="procedure_syndicat" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_PROCEDURE_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_PROCEDURE') .' : </label>
              <input type="checkbox" name="procedure_syndicat" id="procedure_syndicat" value="1" '. ( $data->procedure_syndicat ? 'checked' : '' ) .' />
            </li>
            <li>
              <label for="detail_procedure" class="hasTip" title="'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_PROCEDURE_DETAIL_DESC') .'">'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_PROCEDURE_DETAIL') .' : </label>
              <textarea name="detail_procedure" id="detail_procedure" cols="40" rows="5" >'.$data->detail_procedure .'</textarea>
            </li>
           </ul>
         </fieldset>';

        echo JHtml::_('sliders.panel', 'Loi ALUR', 'alur-pane');
        echo $html;
    }


    /**
     * onAfterShowDescription method (called in the default_item.php tpl)
     *
     * @param stdClass $row
     */
    public function onAfterShowDescription(&$row)
    {
        if (empty($row->alur)) {
            return;
        }

        $data = json_decode($row->alur);
        $title = $this->params->get('title', 'Loi ALUR');
        $html = '';

        if ($this->params->get('calculate_fees_percent', 1) == 1 && !empty($row->fees)) {
            $taux = round(((float) $row->fees * 100) / (float) $row->price, 2);
            $data->taux_honoraires = number_format($taux, 2, ',', ' ');
        }

        if (!empty($title)) {
            $html .= '<h3 class="jea_alur">' . $title .'</h3>' . PHP_EOL;
        }

        if ($this->params->get('display_fields', 1) == 1) {

            $html .= '<table class="jea-data"><tbody>' . PHP_EOL ;
            if (!empty($data->taux_honoraires) && !empty($data->hon_acq)) {
                $html .= '<tr><th>'. JText::_('PLG_JEA_ALUR_FIELD_TAUX_HONORAIRES') .'</th><td>' . $data->taux_honoraires . ' %</td></tr>'. PHP_EOL;
            }
            $html .= '<tr><th>'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE') .'</th><td>' . ($data->copropriete == 1? 'Oui' : 'Non' ) . '</td></tr>'. PHP_EOL;

            if ($data->copropriete == 1) {
                $html .= '<tr><td colspan="2">'. JText::_('PLG_JEA_ALUR_COPROPRIETE_INFOS') .' : </td></tr>' . PHP_EOL
                      . '<tr><th>'. JText::_('PLG_JEA_ALUR_FIELD_NB_LOTS') .'</th><td>' . $data->nb_lots_copropriete . '</td></tr>' . PHP_EOL
                      . '<tr><th>'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_CHARGES') .'</th><td>' . $data->montant_quote_part . ' € / an</td></tr>' . PHP_EOL;
                if ($data->procedure_syndicat == 1) {
                    $html .= '<tr><th>'. JText::_('PLG_JEA_ALUR_FIELD_COPROPRIETE_PROCEDURE') .'</th><td>' . $data->detail_procedure . ' €</td></tr>' . PHP_EOL;
                }
            }
            $html .= '</tbody></table>'. PHP_EOL;
        }
        echo $html;

    }

}
