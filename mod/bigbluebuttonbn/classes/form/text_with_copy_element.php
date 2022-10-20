<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
namespace mod_bigbluebuttonbn\form;

use MoodleQuickForm_text;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->libdir/form/text.php");

/**
 * Text type form element with a copy widget
 *
 * Contains HTML class for a text type element and a link that will copy its content in the copy/paste buffer
 *
 * @package   mod_bigbluebuttonbn
 * @copyright  2022 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Laurent David  (laurent [at] call-learning [dt] fr)
 */
class text_with_copy_element extends MoodleQuickForm_text {
    /**
     * Accepts a renderer
     *
     * @param object $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether an element is required
     * @param string $error An error message associated with an element
     * @return void
     */
    public function accept(&$renderer, $required = false, $error = null) {
        global $OUTPUT;

        // Make sure the element has an id.
        $this->_generateId();
        $advanced = isset($renderer->_advancedElements[$this->getName()]);
        $elementcontext = $this->export_for_template($OUTPUT);

        $helpbutton = '';
        if (method_exists($this, 'getHelpButton')) {
            $helpbutton = $this->getHelpButton();
        }
        $label = $this->getLabel();
        $text = '';
        if (method_exists($this, 'getText')) {
            // There currently exists code that adds a form element with an empty label.
            // If this is the case then set the label to the description.
            if (empty($label)) {
                $label = $this->getText();
            } else {
                $text = $this->getText();
            }
        }

        // Generate the form element wrapper ids and names to pass to the template.
        // This differs between group and non-group elements.
        if ($this->getType() === 'group') {
            // Group element.
            // The id will be something like 'fgroup_id_NAME'. E.g. fgroup_id_mygroup.
            $elementcontext['wrapperid'] = $elementcontext['id'];

            // Ensure group elements pass through the group name as the element name.
            $elementcontext['name'] = $elementcontext['groupname'];
        } else {
            // Non grouped element.
            // Creates an id like 'fitem_id_NAME'. E.g. fitem_id_mytextelement.
            $elementcontext['wrapperid'] = 'fitem_' . $elementcontext['id'];
        }

        $context = array(
                'element' => $elementcontext,
                'label' => $label,
                'text' => $text,
                'required' => $required,
                'advanced' => $advanced,
                'helpbutton' => $helpbutton,
                'error' => $error,
                'copylabel' => $this->_attributes['copylabel'] ?? get_string('copy', 'core_editor')
        );
        $html = $OUTPUT->render_from_template('mod_bigbluebuttonbn/element_text_with_copy', $context);
        if ($renderer->_inGroup) {
            $this->_groupElementTemplate = $html;
        }
        if (($renderer->_inGroup) && !empty($renderer->_groupElementTemplate)) {
            $renderer->_groupElementTemplate = $html;
        } else if (!isset($renderer->_templates[$this->getName()])) {
            $renderer->_templates[$this->getName()] = $html;
        }

        if (in_array($this->getName(), $renderer->_stopFieldsetElements) && $renderer->_fieldsetsOpen > 0) {
            $renderer->_html .= $renderer->_closeFieldsetTemplate;
            $renderer->_fieldsetsOpen--;
        }
        $renderer->_html .= $html;
    }

}

