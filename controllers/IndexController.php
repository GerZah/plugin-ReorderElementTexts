<?php
/**
* ConditionalElements
* @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
* @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
*/

/**
* The Configuration controller.
*
* @package Omeka\Plugins\ConditionalElements
*/
class ReorderElementTexts_IndexController extends Omeka_Controller_AbstractActionController {

  public function checkItemElement() {
		$elements = false;
    $title = "";
		$output = "";

		$returnLink = "<a href='javascript:window.history.back();'>" .
	                __("Please return to the referring page.").
	                "</a>";

	  $itemId = ( isset($_GET["item"]) ? intval($_GET["item"]) : 0 );
	  $elementId = ( isset($_GET["element"]) ? intval($_GET["element"]) : 0 );

	  if (!$itemId) { $output .= __("No item ID specified.") . " " . $returnLink; }
	  else if (!$elementId) { $output .= __("No element ID specified.") . " " . $returnLink; }

	  else {
	    $db = get_db();
	    $itemExists = $db->fetchOne("SELECT count(*) FROM $db->Items WHERE id = $itemId");
	    if (!$itemExists) { $output .= __("Item not found.") . " " . $returnLink; }

	    else {
	      $sql = "SELECT * FROM $db->ElementTexts".
	              " WHERE record_id = $itemId".
	              " AND element_id = $elementId";
	      $elements = $db->fetchAll($sql);
	      if (!$elements) { $output .= __("Specified elements not found in item.") . " " . $returnLink; }
        else {
          $title = __("Item")." #".$itemId;
          $sql = "SELECT id FROM $db->Elements WHERE name='Title'";
          $titleElement = $db->fetchOne($sql);
          if ($titleElement) {
            $sql = "SELECT text".
                    " from $db->ElementTexts".
                    " WHERE record_id=$itemId".
                    " AND element_id=$titleElement".
                    " LIMIT 1";
            $titleVerb = $db->fetchOne($sql);
            if ($titleVerb) { $title .= ": " . $titleVerb;}
          }
        }
			}
		}

		return array("elements" => $elements, "output" => $output, "title" => $title);
	}

  public function reorderAction() {
    queue_js_file('reorderelementtexts_drag');
    queue_css_file('reorderelementtexts_drag');
    $data = SELF::checkItemElement();
    $this->view->elements = $data["elements"];
    $this->view->output = $data["output"];
    $this->view->title = $data["title"];
  }

  public function updateAction() {
    $data = SELF::checkItemElement();

    $this->view->elements = $data["elements"];
    $this->view->output = $data["output"];
    $this->view->title = $data["title"];

    $elements = $data["elements"];
    $output = $data["output"];
    $title = $data["title"];

    if ($elements) {
      // echo "<pre>" . print_r($_GET,true) . "</pre>";
      // echo "<pre>Elements: " . print_r($elements,true) . "</pre>";
      $itemId = intval($_GET["item"]);
  	  $elementId = intval($_GET["element"]);

      $order = json_decode($_GET["reorderElementTextsOrder"]);
      // echo "<pre>Order: " . print_r($order,true) . "</pre>";

      if (count($order) != count($elements)) {
        $returnLink = "<a href='javascript:window.history.back();'>" .
    	                __("Please return to the referring page.").
    	                "</a>";
        $output .= (__("Mismatching number of elements.") . " " . $returnLink);
      }

      else {

        $index = array();
        foreach($elements as $idx => $element) {
          $index[$element["id"]] = $idx;
        }
        // echo "<pre>Index: " . print_r($index,true) . "</pre>";

        $newOrder = array();
        foreach($order as $txt) {
          $newOrder[] = array(
                          "text" => $elements[$index[$txt]]["text"],
                          "html" => $elements[$index[$txt]]["html"]
                        );
        }
        // echo "<pre>NewOrder: " . print_r($newOrder,true) . "</pre>";

        $db = get_db();

        $success = true; # Init -- nothing done yet, so sucess ;-)

        foreach($elements as $idx => $element) {
          $sql = "UPDATE $db->ElementTexts".
                  " SET text='".addslashes($newOrder[$idx]["text"])."',".
                  " html=".$newOrder[$idx]["html"].
                  " WHERE id=".$element["id"];
          $locSuccess = ( $db->query($sql) );
          $success = ( ($success) AND ($locSuccess) ) ;
        }

        $output .= "<p>".
                    ( $success ? __("Reordering successful.") : __("Reordering failed.") ).
                    "</p>";

        $backUrl=url("items/show/".$itemId);
        $output .= "<p><a href='".$backUrl."' class='green button'>".__("Back")."</a></p>";

      }
    }

    $this->view->output = $output;
  }

}
