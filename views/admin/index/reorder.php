<?php
  $pageTitle = __('Reorder Text Elements');
  echo head(array('title'=>$pageTitle));
  # echo flash();

  if ($elements) {
    // echo "<pre>" . print_r($elements,true) . "</pre>";
    $itemId = intval($_GET["item"]);
	  $elementId = intval($_GET["element"]);

    echo "<h3>$title</h3>";
    echo "<h4>$elementTitle</h4>";
    echo "<p><strong>".__("Please select new text element order.")."</strong></p>";
    echo "<p>".__("Simply drag the text elements with your mouse.")."</p>";

    $backUrl=url("items/show/".$itemId);
    echo "<p><a href='".$backUrl."' class='green button'>".__("Cancel")."</a></p>";

    echo "<ul id='sortable'>";
    foreach($elements as $element) {
      $hasRefText = isset($element["refText"]);
      $elementText = ( $hasRefText ? $element["refText"] : $element["text"] );
      echo "<li class='ui-state-default dragitems' data-id='".$element["id"]."'>".
            $elementText.
            "</li>";
    }
    echo "</ul>";

    echo "<form action='".url('reorder-element-texts/index/update')."' method='get'>";
    echo "<input name='item' type='hidden' value='".$itemId."'>";
    echo "<input name='element' type='hidden' value='".$elementId."'>";
    echo "<input id='reorderElementTextsOrder' name='reorderElementTextsOrder' type='hidden' value=''>";
    echo "<input type='submit' value='".__("Reorder Inputs")."'>";
    echo "</form>";
  }

  else { echo $output; }

  echo foot();
?>
